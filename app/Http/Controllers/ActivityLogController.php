<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Booking;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'notes' => 'required|string',
        ]);

        $booking->activityLogs()->create([
            'notes' => $validated['notes'],
            'user_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Activity note added successfully.');
    }

    public function destroy(ActivityLog $activityLog)
    {
        $activityLog->delete();

        return redirect()->back()->with('success', 'Activity note deleted successfully.');
    }
}
