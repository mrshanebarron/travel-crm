<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Group;
use App\Models\Traveler;
use App\Models\SafariDay;
use App\Models\Task;
use App\Models\User;
use App\Services\SafariPdfParser;
use App\Exports\SelectedBookingsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

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
            'safariDays.activities',
            'tasks.assignedTo',
            'documents',
            'rooms',
            'activityLogs.user',
            'ledgerEntries',
            'creator',
            'emailLogs.traveler',
            'emailLogs.sender',
        ]);

        // Get all users for task assignment dropdown
        $users = User::orderBy('name')->get();

        return view('bookings.show', compact('booking', 'users'));
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
            $oldStartDate = $booking->start_date;
            $oldEndDate = $booking->end_date;
            $newStartDate = \Carbon\Carbon::parse($validated['start_date']);
            $newEndDate = \Carbon\Carbon::parse($validated['end_date']);
            $startDateChanged = !$oldStartDate->equalTo($newStartDate);
            $endDateChanged = !$oldEndDate->equalTo($newEndDate);

            $booking->update([
                'country' => $validated['country'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => $validated['status'],
                'guides' => $validated['guides'] ?? [],
            ]);

            // If start date changed, recalculate all payment schedules and log it
            if ($startDateChanged) {
                $daysUntilSafari = now()->diffInDays($newStartDate, false);
                foreach ($booking->groups as $group) {
                    foreach ($group->travelers as $traveler) {
                        if ($traveler->payment) {
                            $traveler->payment->recalculateWithAddons($daysUntilSafari);
                            $traveler->payment->save();
                        }
                    }
                }

                $booking->activityLogs()->create([
                    'user_id' => auth()->id(),
                    'action_type' => 'date_changed',
                    'notes' => 'Start date changed from ' . $oldStartDate->format('M j, Y') . ' to ' . $newStartDate->format('M j, Y'),
                ]);
            }

            // Log end date change separately
            if ($endDateChanged) {
                $booking->activityLogs()->create([
                    'user_id' => auth()->id(),
                    'action_type' => 'date_changed',
                    'notes' => 'End date changed from ' . $oldEndDate->format('M j, Y') . ' to ' . $newEndDate->format('M j, Y'),
                ]);
            }

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

                        // Log traveler added
                        $booking->activityLogs()->create([
                            'user_id' => auth()->id(),
                            'action_type' => 'traveler_added',
                            'notes' => "Traveler added: {$newTraveler->full_name}",
                        ]);
                    }
                }

                // Delete travelers that were removed and log it
                $removedTravelers = $group->travelers()->whereNotIn('id', $existingTravelerIds)->get();
                foreach ($removedTravelers as $removedTraveler) {
                    $booking->activityLogs()->create([
                        'user_id' => auth()->id(),
                        'action_type' => 'traveler_removed',
                        'notes' => "Traveler removed: {$removedTraveler->full_name}",
                    ]);
                }
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
        $this->authorize('update', $booking);

        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:10240',
        ]);

        // Store the uploaded PDF
        $path = $request->file('pdf')->store('safari-pdfs', 'local');
        $fullPath = Storage::disk('local')->path($path);

        try {
            $parser = new SafariPdfParser();
            $parsedDays = $parser->parse($fullPath);

            // Also extract rates from PDF
            $extractedRates = $parser->extractRates();
            $ratesAssigned = 0;

            if (empty($parsedDays) && empty(array_filter($extractedRates))) {
                // Store file as document for manual review
                $booking->documents()->create([
                    'name' => $request->file('pdf')->getClientOriginalName(),
                    'file_path' => $path,
                    'category' => 'misc',
                    'uploaded_by' => auth()->id(),
                ]);

                // Log activity
                $booking->activityLogs()->create([
                    'user_id' => auth()->id(),
                    'action_type' => 'pdf_imported',
                    'notes' => 'PDF uploaded but could not be parsed automatically. Saved for manual review.',
                ]);

                return redirect()->route('bookings.show', $booking)
                    ->with('warning', 'PDF uploaded but we could not extract itinerary data automatically. The file has been saved to Documents for manual review.');
            }

            // Get existing safari days
            $existingDays = $booking->safariDays()->orderBy('day_number')->get()->keyBy('day_number');
            $updatedCount = 0;

            DB::transaction(function () use ($parsedDays, $booking, $existingDays, &$updatedCount, $extractedRates, &$ratesAssigned) {
                // Update itinerary days
                foreach ($parsedDays as $dayData) {
                    $dayNumber = $dayData['day_number'];

                    if ($existingDays->has($dayNumber)) {
                        // Update existing day - only fill in empty fields
                        $existingDay = $existingDays[$dayNumber];
                        $updates = [];

                        if (empty($existingDay->location) && !empty($dayData['location'])) {
                            $updates['location'] = $dayData['location'];
                        }
                        if (empty($existingDay->lodge) && !empty($dayData['lodge'])) {
                            $updates['lodge'] = $dayData['lodge'];
                        }
                        if (empty($existingDay->morning_activity) && !empty($dayData['morning_activity'])) {
                            $updates['morning_activity'] = $dayData['morning_activity'];
                        }
                        if (empty($existingDay->midday_activity) && !empty($dayData['midday_activity'])) {
                            $updates['midday_activity'] = $dayData['midday_activity'];
                        }
                        if (empty($existingDay->afternoon_activity) && !empty($dayData['afternoon_activity'])) {
                            $updates['afternoon_activity'] = $dayData['afternoon_activity'];
                        }
                        if (empty($existingDay->other_activities) && !empty($dayData['other_activities'])) {
                            $updates['other_activities'] = $dayData['other_activities'];
                        }
                        if (empty($existingDay->meal_plan) && !empty($dayData['meal_plan'])) {
                            $updates['meal_plan'] = $dayData['meal_plan'];
                        }
                        if (empty($existingDay->drink_plan) && !empty($dayData['drink_plan'])) {
                            $updates['drink_plan'] = $dayData['drink_plan'];
                        }

                        if (!empty($updates)) {
                            $existingDay->update($updates);
                            $updatedCount++;
                        }
                    }
                }

                // Assign rates to travelers based on age category
                if (!empty(array_filter($extractedRates))) {
                    $travelers = $booking->groups->flatMap->travelers;

                    foreach ($travelers as $traveler) {
                        // Skip if traveler already has a payment record
                        if ($traveler->payment) {
                            continue;
                        }

                        $ageCategory = $traveler->age_category;
                        $rate = $extractedRates[$ageCategory] ?? $extractedRates['adult'] ?? null;

                        if ($rate && $rate > 0) {
                            // Calculate days until safari for payment schedule
                            $daysUntilSafari = now()->startOfDay()->diffInDays($booking->start_date, false);

                            $payment = new \App\Models\Payment();
                            $payment->traveler_id = $traveler->id;
                            $payment->safari_rate = $rate;
                            $payment->recalculateSchedule($daysUntilSafari);
                            $payment->save();

                            $ratesAssigned++;
                        }
                    }
                }
            });

            // Store the PDF as a document
            $booking->documents()->create([
                'name' => $request->file('pdf')->getClientOriginalName(),
                'file_path' => $path,
                'category' => 'misc',
                'uploaded_by' => auth()->id(),
            ]);

            // Build success message
            $messages = [];
            if ($updatedCount > 0) {
                $messages[] = "{$updatedCount} days updated with itinerary data";
            }
            if ($ratesAssigned > 0) {
                $messages[] = "{$ratesAssigned} traveler rates assigned based on age";
            }

            $description = "Safari Office PDF imported. " . implode('. ', $messages) . ".";

            // Log activity
            $booking->activityLogs()->create([
                'user_id' => auth()->id(),
                'action_type' => 'pdf_imported',
                'notes' => $description,
            ]);

            $successMessage = "PDF imported successfully!";
            if (!empty($messages)) {
                $successMessage .= " " . implode('. ', $messages) . ".";
            }

            return redirect()->route('bookings.show', ['booking' => $booking->id, 'tab' => 'payment-details'])
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('PDF import failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);

            // Still save the file for manual review
            $booking->documents()->create([
                'name' => $request->file('pdf')->getClientOriginalName(),
                'file_path' => $path,
                'category' => 'misc',
                'uploaded_by' => auth()->id(),
            ]);

            return redirect()->route('bookings.show', $booking)
                ->with('warning', 'There was an issue parsing the PDF automatically. The file has been saved to Documents for manual review.');
        }
    }

    /**
     * Export selected bookings to Excel.
     */
    public function bulkExport(Request $request)
    {
        $request->validate([
            'booking_ids' => 'required|string',
        ]);

        $ids = array_filter(explode(',', $request->booking_ids));

        if (empty($ids)) {
            return back()->with('error', 'No bookings selected for export.');
        }

        // Check authorization for each booking
        $bookings = Booking::whereIn('id', $ids)->get();
        foreach ($bookings as $booking) {
            $this->authorize('view', $booking);
        }

        return Excel::download(new SelectedBookingsExport($ids), 'selected-bookings-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Update status of multiple bookings.
     */
    public function bulkStatus(Request $request)
    {
        $request->validate([
            'booking_ids' => 'required|string',
            'status' => 'required|in:upcoming,active,completed',
        ]);

        $ids = array_filter(explode(',', $request->booking_ids));

        if (empty($ids)) {
            return back()->with('error', 'No bookings selected.');
        }

        $bookings = Booking::whereIn('id', $ids)->get();
        $updatedCount = 0;

        foreach ($bookings as $booking) {
            $this->authorize('update', $booking);
            $booking->update(['status' => $request->status]);
            $updatedCount++;
        }

        $statusLabels = [
            'upcoming' => 'Upcoming',
            'active' => 'Active',
            'completed' => 'Completed',
        ];

        return back()->with('success', "{$updatedCount} booking(s) marked as {$statusLabels[$request->status]}.");
    }

    /**
     * Create default tasks for a new booking based on the Master Checklist template.
     * Tasks are triggered based on timing relative to the safari start date.
     * Assignments are based on the team_members config mapping.
     */
    private function createDefaultTasks(Booking $booking): void
    {
        $defaultTasks = config('booking_tasks.default_tasks', []);
        $teamMembers = config('booking_tasks.team_members', []);

        $safariStartDate = $booking->start_date;
        $safariEndDate = $booking->end_date;
        $bookingCreatedDate = now();

        foreach ($defaultTasks as $taskData) {
            $dueDate = null;

            if (!empty($taskData['on_create'])) {
                // Immediate tasks - due tomorrow
                $dueDate = now()->addDays(1);
            } elseif (!empty($taskData['days_after_booking'])) {
                // Tasks relative to booking creation date
                $dueDate = $bookingCreatedDate->copy()->addDays($taskData['days_after_booking']);
            } elseif (!empty($taskData['days_before'])) {
                // Tasks relative to safari start date
                $dueDate = $safariStartDate->copy()->subDays($taskData['days_before']);
                // Don't create tasks with due dates in the past
                if ($dueDate->lt(now())) {
                    $dueDate = now();
                }
            } elseif (!empty($taskData['days_after'])) {
                // Post-safari tasks
                $dueDate = $safariEndDate->copy()->addDays($taskData['days_after']);
            }

            // Look up assigned user by name from team_members config
            $assignedTo = null;
            if (!empty($taskData['assigned_to_name'])) {
                $memberName = $taskData['assigned_to_name'];
                // Try to find user by name (case-insensitive partial match)
                $assignedTo = User::where('name', 'like', "%{$memberName}%")->first()?->id;
            }

            $booking->tasks()->create([
                'name' => $taskData['name'],
                'status' => 'pending',
                'due_date' => $dueDate,
                'days_before_safari' => $taskData['days_before'] ?? null,
                'timing_description' => $taskData['timing_description'] ?? null,
                'assigned_to' => $assignedTo,
                'assigned_at' => $assignedTo ? now() : null,
                'assigned_by' => auth()->id(),
            ]);
        }
    }

    /**
     * Create a new booking from a Safari Office PDF upload.
     * Extracts metadata, creates booking with travelers, safari days, and rates.
     */
    public function createFromPdf(Request $request)
    {
        $this->authorize('create', Booking::class);

        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:10240',
        ]);

        // Store the uploaded PDF temporarily
        $path = $request->file('pdf')->store('safari-pdfs', 'local');
        $fullPath = Storage::disk('local')->path($path);

        try {
            $parser = new SafariPdfParser();
            $parsedDays = $parser->parse($fullPath);
            $metadata = $parser->extractBookingMetadata();
            $extractedRates = $parser->extractRates();

            // Validate we have minimum required data
            if (empty($metadata['start_date']) || empty($metadata['end_date'])) {
                Storage::disk('local')->delete($path);
                return redirect()->route('bookings.index')
                    ->with('error', 'Could not extract booking dates from the PDF. Please create the booking manually.');
            }

            $booking = DB::transaction(function () use ($metadata, $parsedDays, $extractedRates, $path, $request) {
                // Create the booking
                $booking = Booking::create([
                    'booking_number' => Booking::generateBookingNumber(),
                    'country' => $metadata['country'] ?? 'Kenya',
                    'start_date' => $metadata['start_date'],
                    'end_date' => $metadata['end_date'],
                    'guides' => [],
                    'created_by' => auth()->id(),
                ]);

                // Create Group 1
                $group = $booking->groups()->create(['group_number' => 1]);

                // Create lead traveler
                if ($metadata['lead_first_name']) {
                    $leadTraveler = $group->travelers()->create([
                        'first_name' => $metadata['lead_first_name'],
                        'last_name' => $metadata['lead_last_name'] ?? '',
                        'is_lead' => true,
                        'order' => 0,
                    ]);

                    // Create payment record for lead if we have a rate
                    $rate = $extractedRates['adult'] ?? $metadata['adult_rate'] ?? null;
                    if ($rate && $rate > 0) {
                        $daysUntilSafari = now()->startOfDay()->diffInDays($booking->start_date, false);
                        $payment = new \App\Models\Payment();
                        $payment->traveler_id = $leadTraveler->id;
                        $payment->safari_rate = $rate;
                        $payment->recalculateSchedule($daysUntilSafari);
                        $payment->save();
                    }
                }

                // Create placeholder travelers for remaining count
                $remainingAdults = max(0, ($metadata['adult_count'] ?? 1) - 1);
                $childCount = $metadata['child_count'] ?? 0;

                for ($i = 0; $i < $remainingAdults; $i++) {
                    $traveler = $group->travelers()->create([
                        'first_name' => 'Adult',
                        'last_name' => ($i + 2),
                        'is_lead' => false,
                        'order' => $i + 1,
                    ]);

                    // Create payment record
                    $rate = $extractedRates['adult'] ?? $metadata['adult_rate'] ?? null;
                    if ($rate && $rate > 0) {
                        $daysUntilSafari = now()->startOfDay()->diffInDays($booking->start_date, false);
                        $payment = new \App\Models\Payment();
                        $payment->traveler_id = $traveler->id;
                        $payment->safari_rate = $rate;
                        $payment->recalculateSchedule($daysUntilSafari);
                        $payment->save();
                    }
                }

                for ($i = 0; $i < $childCount; $i++) {
                    $traveler = $group->travelers()->create([
                        'first_name' => 'Child',
                        'last_name' => ($i + 1),
                        'is_lead' => false,
                        'order' => $remainingAdults + $i + 1,
                    ]);

                    // Create payment record with child rate
                    $rate = $extractedRates['child_2_11'] ?? $extractedRates['child_12_17'] ?? $metadata['child_rate'] ?? null;
                    if ($rate && $rate > 0) {
                        $daysUntilSafari = now()->startOfDay()->diffInDays($booking->start_date, false);
                        $payment = new \App\Models\Payment();
                        $payment->traveler_id = $traveler->id;
                        $payment->safari_rate = $rate;
                        $payment->recalculateSchedule($daysUntilSafari);
                        $payment->save();
                    }
                }

                // Create safari days from parsed itinerary or date range
                if (!empty($parsedDays)) {
                    foreach ($parsedDays as $dayData) {
                        $safariDay = $booking->safariDays()->create([
                            'day_number' => $dayData['day_number'],
                            'date' => $dayData['date'] ?? null,
                            'location' => $dayData['location'] ?? '',
                            'lodge' => $dayData['lodge'] ?? null,
                            'meal_plan' => $dayData['meal_plan'] ?? null,
                            'drink_plan' => $dayData['drink_plan'] ?? null,
                        ]);

                        // Create activities from parsed data
                        $this->createActivitiesFromParsedData($safariDay, $dayData);
                    }
                } else {
                    // Fallback: create empty days based on date range
                    $startDate = \Carbon\Carbon::parse($metadata['start_date']);
                    $endDate = \Carbon\Carbon::parse($metadata['end_date']);
                    $dayNumber = 1;

                    for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                        $booking->safariDays()->create([
                            'day_number' => $dayNumber++,
                            'date' => $date->format('Y-m-d'),
                            'location' => '',
                        ]);
                    }
                }

                // Store the PDF as a document
                $booking->documents()->create([
                    'name' => $request->file('pdf')->getClientOriginalName(),
                    'file_path' => $path,
                    'category' => 'misc',
                    'uploaded_by' => auth()->id(),
                ]);

                // Auto-populate Master Checklist with default tasks
                $this->createDefaultTasks($booking);

                // Log activity
                $refInfo = $metadata['reference_number'] ? " (Ref: {$metadata['reference_number']})" : '';
                $booking->activityLogs()->create([
                    'user_id' => auth()->id(),
                    'action_type' => 'booking_created',
                    'notes' => "Booking created from Safari Office PDF{$refInfo}. Lead: {$metadata['lead_name']}, {$metadata['traveler_count']} travelers.",
                ]);

                return $booking;
            });

            return redirect()->route('bookings.show', $booking)
                ->with('success', "Booking created from Safari Office PDF! Lead traveler: {$metadata['lead_name']}, {$metadata['traveler_count']} travelers imported.");

        } catch (\Exception $e) {
            Storage::disk('local')->delete($path);

            \Log::error('Create booking from PDF failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('bookings.index')
                ->with('error', 'Failed to create booking from PDF: ' . $e->getMessage());
        }
    }

    /**
     * Import safari itinerary from Safari Office URL.
     */
    public function importUrl(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $request->validate([
            'safari_office_url' => ['required', 'url', 'regex:/^https:\/\/[a-z0-9-]+\.safarioffice\.app\/[a-z0-9-]+\/online$/i'],
        ], [
            'safari_office_url.regex' => 'Please enter a valid Safari Office online booking URL.',
        ]);

        try {
            $scraper = new \App\Services\SafariOfficeWebScraper();
            $parsedDays = $scraper->parse($request->safari_office_url);

            if (empty($parsedDays)) {
                // Log activity
                $booking->activityLogs()->create([
                    'user_id' => auth()->id(),
                    'action_type' => 'url_import_failed',
                    'notes' => 'Safari Office URL imported but no itinerary data could be extracted.',
                ]);

                return redirect()->route('bookings.show', $booking)
                    ->with('warning', 'Could not extract itinerary data from the Safari Office page. Please try importing a PDF instead.');
            }

            // Get existing safari days
            $existingDays = $booking->safariDays()->orderBy('day_number')->get()->keyBy('day_number');
            $updatedCount = 0;

            DB::transaction(function () use ($parsedDays, $booking, $existingDays, &$updatedCount) {
                foreach ($parsedDays as $dayData) {
                    $dayNumber = $dayData['day_number'];

                    if ($existingDays->has($dayNumber)) {
                        // Update existing day - only fill in empty fields
                        $existingDay = $existingDays[$dayNumber];
                        $updates = [];

                        if (empty($existingDay->location) && !empty($dayData['location'])) {
                            $updates['location'] = $dayData['location'];
                        }
                        if (empty($existingDay->lodge) && !empty($dayData['lodge'])) {
                            $updates['lodge'] = $dayData['lodge'];
                        }
                        if (empty($existingDay->morning_activity) && !empty($dayData['morning_activity'])) {
                            $updates['morning_activity'] = $dayData['morning_activity'];
                        }
                        if (empty($existingDay->midday_activity) && !empty($dayData['midday_activity'])) {
                            $updates['midday_activity'] = $dayData['midday_activity'];
                        }
                        if (empty($existingDay->afternoon_activity) && !empty($dayData['afternoon_activity'])) {
                            $updates['afternoon_activity'] = $dayData['afternoon_activity'];
                        }
                        if (empty($existingDay->other_activities) && !empty($dayData['other_activities'])) {
                            $updates['other_activities'] = $dayData['other_activities'];
                        }
                        if (empty($existingDay->meal_plan) && !empty($dayData['meal_plan'])) {
                            $updates['meal_plan'] = $dayData['meal_plan'];
                        }
                        if (empty($existingDay->drink_plan) && !empty($dayData['drink_plan'])) {
                            $updates['drink_plan'] = $dayData['drink_plan'];
                        }

                        if (!empty($updates)) {
                            $existingDay->update($updates);
                            $updatedCount++;
                        }
                    }
                }
            });

            // Store the URL in a note field or as activity log
            $booking->activityLogs()->create([
                'user_id' => auth()->id(),
                'action_type' => 'url_imported',
                'notes' => "Safari Office URL imported. {$updatedCount} days updated with itinerary data. URL: {$request->safari_office_url}",
            ]);

            // Also store the URL in the booking's notes for reference
            if (empty($booking->safari_office_url)) {
                $booking->update(['safari_office_url' => $request->safari_office_url]);
            }

            return redirect()->route('bookings.show', $booking)
                ->with('success', "Safari Office itinerary imported successfully! {$updatedCount} safari days were updated.");

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Safari Office URL import failed', [
                'booking_id' => $booking->id,
                'url' => $request->safari_office_url,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('bookings.show', $booking)
                ->with('error', 'Failed to import from Safari Office: ' . $e->getMessage());
        }
    }

    /**
     * Create activity records from parsed PDF data.
     */
    protected function createActivitiesFromParsedData($safariDay, array $dayData): void
    {
        $periods = [
            'morning' => $dayData['morning_activity'] ?? null,
            'midday' => $dayData['midday_activity'] ?? null,
            'afternoon' => $dayData['afternoon_activity'] ?? null,
        ];

        foreach ($periods as $period => $activityText) {
            if ($activityText) {
                // Split by semicolons (from PDF parser concatenation) or newlines
                $activities = preg_split('/[;\n]/', $activityText);
                $sortOrder = 0;

                foreach ($activities as $activity) {
                    $activity = trim($activity);
                    if ($activity) {
                        $safariDay->activities()->create([
                            'period' => $period,
                            'activity' => $activity,
                            'sort_order' => $sortOrder++,
                        ]);
                    }
                }
            }
        }
    }
}
