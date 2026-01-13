<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Group;
use App\Models\Traveler;
use App\Models\SafariDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
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
        return view('bookings.create');
    }

    public function store(Request $request)
    {
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

            return $booking;
        });

        return redirect()->route('bookings.index')
            ->with('success', 'Booking created successfully.');
    }

    public function show(Booking $booking)
    {
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
        $booking->load(['groups.travelers', 'safariDays']);
        return view('bookings.edit', compact('booking'));
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'country' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:upcoming,active,completed',
            'guides' => 'nullable|array',
        ]);

        $booking->update($validated);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking updated successfully.');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();

        return redirect()->route('bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }
}
