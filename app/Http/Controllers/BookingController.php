<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Group;
use App\Models\Traveler;
use App\Models\SafariDay;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Booking::class);

        $query = Booking::with(['groups.travelers', 'creator']);

        // Filter by status if provided
        if ($request->has('status') && in_array($request->status, ['upcoming', 'active', 'completed'])) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderBy('start_date', 'desc')->paginate(15);

        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        $this->authorize('create', Booking::class);
        return view('bookings.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Booking::class);

        $validated = $request->validate([
            'country' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'guides' => 'nullable|array',
            'travelers' => 'required|array|min:1',
            'travelers.*.first_name' => 'required|string|max:255',
            'travelers.*.last_name' => 'required|string|max:255',
            'travelers.*.email' => 'nullable|email|max:255',
            'travelers.*.phone' => 'nullable|string|max:255',
            'travelers.*.dob' => 'nullable|date',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $booking = Booking::create([
                'booking_number' => Booking::generateBookingNumber(),
                'country' => $validated['country'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'guides' => $validated['guides'] ?? [],
                'created_by' => auth()->id(),
            ]);

            // Create Group 1 with all travelers
            $group = $booking->groups()->create(['group_number' => 1]);

            foreach ($validated['travelers'] as $index => $travelerData) {
                $group->travelers()->create([
                    'first_name' => $travelerData['first_name'],
                    'last_name' => $travelerData['last_name'],
                    'email' => $travelerData['email'] ?? null,
                    'phone' => $travelerData['phone'] ?? null,
                    'dob' => $travelerData['dob'] ?? null,
                    'is_lead' => $index === 0,
                    'order' => $index,
                ]);
            }

            // Create safari days based on date range
            $startDate = \Carbon\Carbon::parse($validated['start_date']);
            $endDate = \Carbon\Carbon::parse($validated['end_date']);
            $dayNumber = 1;

            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                $booking->safariDays()->create([
                    'day_number' => $dayNumber++,
                    'date' => $date->format('Y-m-d'),
                    'location' => '',
                ]);
            }

            // Auto-populate Master Checklist with default tasks
            $this->createDefaultTasks($booking);

            return $booking;
        });

        return redirect()->route('bookings.index')
            ->with('success', 'Booking created successfully.');
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load([
            'groups.travelers.flights',
            'groups.travelers.payment',
            'safariDays',
            'tasks',
            'documents',
            'rooms',
            'activityLogs.user',
            'ledgerEntries',
            'creator',
        ]);

        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $this->authorize('update', $booking);
        $booking->load(['groups.travelers', 'safariDays']);
        return view('bookings.edit', compact('booking'));
    }

    public function update(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $validated = $request->validate([
            'country' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:upcoming,active,completed',
            'guides' => 'nullable|array',
            'travelers' => 'sometimes|array',
            'travelers.*.id' => 'nullable|exists:travelers,id',
            'travelers.*.first_name' => 'required|string|max:255',
            'travelers.*.last_name' => 'required|string|max:255',
            'travelers.*.email' => 'nullable|email|max:255',
            'travelers.*.phone' => 'nullable|string|max:255',
            'travelers.*.dob' => 'nullable|date',
        ]);

        DB::transaction(function () use ($validated, $booking) {
            $booking->update([
                'country' => $validated['country'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => $validated['status'],
                'guides' => $validated['guides'] ?? [],
            ]);

            // Update travelers if provided
            if (isset($validated['travelers'])) {
                $group = $booking->groups()->first();
                $existingTravelerIds = [];

                foreach ($validated['travelers'] as $index => $travelerData) {
                    if (!empty($travelerData['id'])) {
                        // Update existing traveler
                        $traveler = Traveler::find($travelerData['id']);
                        if ($traveler) {
                            $traveler->update([
                                'first_name' => $travelerData['first_name'],
                                'last_name' => $travelerData['last_name'],
                                'email' => $travelerData['email'] ?? null,
                                'phone' => $travelerData['phone'] ?? null,
                                'dob' => $travelerData['dob'] ?? null,
                            ]);
                            $existingTravelerIds[] = $traveler->id;
                        }
                    } else {
                        // Create new traveler
                        $newTraveler = $group->travelers()->create([
                            'first_name' => $travelerData['first_name'],
                            'last_name' => $travelerData['last_name'],
                            'email' => $travelerData['email'] ?? null,
                            'phone' => $travelerData['phone'] ?? null,
                            'dob' => $travelerData['dob'] ?? null,
                            'is_lead' => $index === 0 && $group->travelers()->count() === 0,
                            'order' => $index,
                        ]);
                        $existingTravelerIds[] = $newTraveler->id;
                    }
                }

                // Delete travelers that were removed
                $group->travelers()->whereNotIn('id', $existingTravelerIds)->delete();
            }
        });

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking updated successfully.');
    }

    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);

        $booking->delete();

        return redirect()->route('bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }

    public function importPdf(Request $request, Booking $booking)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:10240',
        ]);

        // Store the uploaded PDF
        $path = $request->file('pdf')->store('safari-pdfs', 'local');

        // For now, we'll add a placeholder message. PDF parsing to be implemented.
        // The Safari Office PDF format has been analyzed and can be parsed for:
        // - Day-by-day itinerary
        // - Accommodations
        // - Activities
        // - Meal plans

        return redirect()->route('bookings.show', $booking)
            ->with('info', 'PDF uploaded. Automatic parsing coming soon - please enter itinerary manually for now.');
    }

    /**
     * Create default tasks for a new booking based on the Master Checklist template.
     * Tasks are triggered based on timing relative to the safari start date.
     */
    private function createDefaultTasks(Booking $booking): void
    {
        $defaultTasks = config('booking_tasks.default_tasks', []);

        $safariStartDate = $booking->start_date;
        $safariEndDate = $booking->end_date;

        foreach ($defaultTasks as $taskData) {
            $dueDate = null;

            if (!empty($taskData['on_create'])) {
                $dueDate = now()->addDays(1);
            } elseif (!empty($taskData['days_before'])) {
                $dueDate = $safariStartDate->copy()->subDays($taskData['days_before']);
                // Don't create tasks with due dates in the past
                if ($dueDate->lt(now())) {
                    $dueDate = now();
                }
            } elseif (!empty($taskData['days_after'])) {
                $dueDate = $safariEndDate->copy()->addDays($taskData['days_after']);
            }

            $booking->tasks()->create([
                'name' => $taskData['name'],
                'status' => 'pending',
                'due_date' => $dueDate,
                'days_before_safari' => $taskData['days_before'] ?? null,
                'assigned_by' => auth()->id(),
            ]);
        }
    }
}
