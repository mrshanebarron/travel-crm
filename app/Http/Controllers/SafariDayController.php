<?php

namespace App\Http\Controllers;

use App\Models\SafariDay;
use App\Models\SafariDayActivity;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SafariDayController extends Controller
{
    public function update(Request $request, SafariDay $safariDay)
    {
        Gate::authorize('update', $safariDay->booking);

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

    public function updateActivities(Request $request, SafariDay $safariDay)
    {
        Gate::authorize('update', $safariDay->booking);

        $validated = $request->validate([
            'period' => 'required|in:morning,midday,afternoon,evening',
            'activities' => 'array',
            'activities.*' => 'string|max:255',
        ]);

        // Delete existing activities for this period
        $safariDay->activities()->where('period', $validated['period'])->delete();

        // Create new activities
        foreach ($validated['activities'] as $index => $activity) {
            $safariDay->activities()->create([
                'period' => $validated['period'],
                'activity' => $activity,
                'sort_order' => $index,
            ]);
        }

        return response()->json(['success' => true]);
    }
}
