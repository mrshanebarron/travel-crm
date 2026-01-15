<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use App\Models\Traveler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FlightController extends Controller
{
    public function store(Request $request, Traveler $traveler)
    {
        Gate::authorize('update', $traveler->group->booking);

        $validated = $request->validate([
            'type' => 'required|in:arrival,departure',
            'airport' => 'required|string|max:255',
            'flight_number' => 'nullable|string|max:50',
            'date' => 'nullable|date',
            'time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
            'pickup_instructions' => 'nullable|string',
            'dropoff_instructions' => 'nullable|string',
        ]);

        $traveler->flights()->create($validated);

        return redirect()->back()->with('success', 'Flight added successfully.');
    }

    public function update(Request $request, Flight $flight)
    {
        Gate::authorize('update', $flight->traveler->group->booking);

        $validated = $request->validate([
            'type' => 'required|in:arrival,departure',
            'airport' => 'required|string|max:255',
            'flight_number' => 'nullable|string|max:50',
            'date' => 'nullable|date',
            'time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
            'pickup_instructions' => 'nullable|string',
            'dropoff_instructions' => 'nullable|string',
        ]);

        $flight->update($validated);

        return redirect()->back()->with('success', 'Flight updated successfully.');
    }

    public function destroy(Flight $flight)
    {
        Gate::authorize('update', $flight->traveler->group->booking);

        $flight->delete();

        return redirect()->back()->with('success', 'Flight removed successfully.');
    }

    public function copyToTravelers(Request $request, Flight $flight)
    {
        Gate::authorize('update', $flight->traveler->group->booking);

        $validated = $request->validate([
            'traveler_ids' => 'required|array|min:1',
            'traveler_ids.*' => 'exists:travelers,id',
        ]);

        $booking = $flight->traveler->group->booking;
        $copiedCount = 0;

        foreach ($validated['traveler_ids'] as $travelerId) {
            // Make sure traveler belongs to the same booking
            $traveler = Traveler::find($travelerId);
            if ($traveler && $traveler->group->booking_id === $booking->id) {
                // Check if traveler already has this exact flight to avoid duplicates
                $exists = $traveler->flights()
                    ->where('type', $flight->type)
                    ->where('airport', $flight->airport)
                    ->where('flight_number', $flight->flight_number)
                    ->where('date', $flight->date)
                    ->exists();

                if (!$exists) {
                    $traveler->flights()->create([
                        'type' => $flight->type,
                        'airport' => $flight->airport,
                        'flight_number' => $flight->flight_number,
                        'date' => $flight->date,
                        'time' => $flight->time,
                        'notes' => $flight->notes,
                        'pickup_instructions' => $flight->pickup_instructions,
                        'dropoff_instructions' => $flight->dropoff_instructions,
                    ]);
                    $copiedCount++;
                }
            }
        }

        if ($copiedCount > 0) {
            return redirect()->back()->with('success', "Flight copied to {$copiedCount} traveler(s).");
        }

        return redirect()->back()->with('info', 'No new flights were copied (travelers may already have this flight).');
    }
}
