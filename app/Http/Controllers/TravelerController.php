<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Traveler;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TravelerController extends Controller
{
    public function store(Request $request, Group $group)
    {
        // Authorize via parent booking
        Gate::authorize('update', $group->booking);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
        ]);

        $maxOrder = $group->travelers()->max('order') ?? -1;

        $traveler = $group->travelers()->create([
            ...$validated,
            'is_lead' => false,
            'order' => $maxOrder + 1,
        ]);

        ActivityLog::logAction(
            $group->booking_id,
            'traveler_added',
            "Added traveler: {$traveler->first_name} {$traveler->last_name} to Group {$group->group_number}",
            'Traveler',
            $traveler->id
        );

        return redirect()->back()->with('success', 'Traveler added successfully.');
    }

    public function update(Request $request, Traveler $traveler)
    {
        // Authorize via parent booking
        Gate::authorize('update', $traveler->group->booking);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
        ]);

        $traveler->update($validated);

        return redirect()->back()->with('success', 'Traveler updated successfully.');
    }

    public function destroy(Traveler $traveler)
    {
        // Authorize via parent booking
        Gate::authorize('update', $traveler->group->booking);

        $traveler->delete();

        return redirect()->back()->with('success', 'Traveler removed successfully.');
    }
}
