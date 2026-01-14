<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GroupController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        // Get the next group number for this booking
        $nextGroupNumber = $booking->groups()->max('group_number') + 1;

        $booking->groups()->create([
            'group_number' => $nextGroupNumber,
        ]);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Group created successfully.');
    }

    public function destroy(Group $group)
    {
        Gate::authorize('update', $group->booking);

        $booking = $group->booking;

        // Delete all travelers in the group first
        $group->travelers()->delete();
        $group->delete();

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Group deleted successfully.');
    }
}
