<?php

namespace App\Http\Controllers;

use App\Models\Traveler;
use App\Models\Group;
use Illuminate\Http\Request;

class TravelerController extends Controller
{
    public function store(Request $request, Group $group)
    {
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

        return redirect()->back()->with('success', 'Traveler added successfully.');
    }

    public function update(Request $request, Traveler $traveler)
    {
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
        $traveler->delete();

        return redirect()->back()->with('success', 'Traveler removed successfully.');
    }
}
