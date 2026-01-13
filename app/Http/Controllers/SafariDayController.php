<?php

namespace App\Http\Controllers;

use App\Models\SafariDay;
use App\Models\Booking;
use Illuminate\Http\Request;

class SafariDayController extends Controller
{
    public function update(Request $request, SafariDay $safariDay)
    {
        $validated = $request->validate([
            'location' => 'required|string|max:255',
            'lodge' => 'nullable|string|max:255',
            'morning_activity' => 'nullable|string|max:255',
            'midday_activity' => 'nullable|string|max:255',
            'afternoon_activity' => 'nullable|string|max:255',
            'other_activities' => 'nullable|string|max:255',
            'meal_plan' => 'nullable|string|max:50',
            'drink_plan' => 'nullable|string|max:50',
        ]);

        $safariDay->update($validated);

        return redirect()->back()->with('success', 'Safari day updated successfully.');
    }
}
