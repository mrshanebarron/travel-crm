<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ActivityLogController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

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
        Gate::authorize('update', $activityLog->booking);

        // Only users with modify_activity_log permission can delete activity logs
        if (!auth()->user()->can('modify_activity_log')) {
            return redirect()->back()->with('error', 'Only super administrators can delete activity log entries.');
        }

        $activityLog->delete();

        return redirect()->back()->with('success', 'Activity note deleted successfully.');
    }
}
