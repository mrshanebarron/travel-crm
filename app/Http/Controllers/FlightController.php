<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use App\Models\Traveler;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    public function store(Request $request, Traveler $traveler)
    {
        $validated = $request->validate([
            'type' => 'required|in:arrival,departure',
            'airport' => 'required|string|max:255',
            'flight_number' => 'nullable|string|max:50',
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        $traveler->flights()->create($validated);

        return redirect()->back()->with('success', 'Flight added successfully.');
    }

    public function update(Request $request, Flight $flight)
    {
        $validated = $request->validate([
            'type' => 'required|in:arrival,departure',
            'airport' => 'required|string|max:255',
            'flight_number' => 'nullable|string|max:50',
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        $flight->update($validated);

        return redirect()->back()->with('success', 'Flight updated successfully.');
    }

    public function destroy(Flight $flight)
    {
        $flight->delete();

        return redirect()->back()->with('success', 'Flight removed successfully.');
    }
}
