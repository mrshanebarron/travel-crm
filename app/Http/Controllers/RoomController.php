<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoomController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $validated = $request->validate([
            'type' => 'required|in:double,triple,single,family,other',
            'custom_type' => 'nullable|required_if:type,other|string|max:255',
            'group_id' => 'nullable|exists:groups,id',
            'adults' => 'required|integer|min:0',
            'children_12_17' => 'nullable|integer|min:0',
            'children_2_11' => 'nullable|integer|min:0',
            'children_under_2' => 'nullable|integer|min:0',
        ]);

        $booking->rooms()->create($validated);

        return redirect()->back()->with('success', 'Room added successfully.');
    }

    public function update(Request $request, Room $room)
    {
        Gate::authorize('update', $room->booking);

        $validated = $request->validate([
            'type' => 'required|in:double,triple,single,family,other',
            'custom_type' => 'nullable|required_if:type,other|string|max:255',
            'adults' => 'required|integer|min:0',
            'children_12_17' => 'nullable|integer|min:0',
            'children_2_11' => 'nullable|integer|min:0',
            'children_under_2' => 'nullable|integer|min:0',
        ]);

        $room->update($validated);

        return redirect()->back()->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        Gate::authorize('update', $room->booking);

        $room->delete();

        return redirect()->back()->with('success', 'Room removed successfully.');
    }
}
