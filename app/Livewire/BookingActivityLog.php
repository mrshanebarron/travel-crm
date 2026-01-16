<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\Booking;
use Livewire\Component;

class BookingActivityLog extends Component
{
    public Booking $booking;
    public $notes = '';

    public function mount(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function addNote()
    {
        $this->validate([
            'notes' => 'required|string|max:2000',
        ]);

        $this->booking->activityLogs()->create([
            'user_id' => auth()->id(),
            'action_type' => 'manual',
            'notes' => $this->notes,
        ]);

        $this->notes = '';
        $this->booking->refresh();
    }

    public function deleteLog(ActivityLog $log)
    {
        if ($log->action_type === 'manual') {
            $log->delete();
            $this->booking->refresh();
        }
    }

    public function render()
    {
        return view('livewire.booking-activity-log', [
            'logs' => $this->booking->activityLogs()->with('user')->latest()->get(),
        ]);
    }
}
