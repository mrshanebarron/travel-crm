<?php

namespace App\Livewire;

use App\Models\Booking;
use Livewire\Component;
use Livewire\WithPagination;

class BookingsList extends Component
{
    use WithPagination;

    public $status = '';
    public $selected = [];
    public $selectAll = false;

    protected $queryString = ['status'];

    public function mount()
    {
        $this->status = request('status', '');
    }

    public function setStatus($status)
    {
        $this->status = $status;
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->getBookingsProperty()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelected()
    {
        $bookingIds = $this->getBookingsProperty()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = count($this->selected) === count($bookingIds) && count($bookingIds) > 0;
    }

    public function clearSelection()
    {
        $this->selected = [];
        $this->selectAll = false;
    }

    public function bulkUpdateStatus($newStatus)
    {
        if (empty($this->selected)) {
            return;
        }

        Booking::whereIn('id', $this->selected)->update(['status' => $newStatus]);
        $this->clearSelection();
    }

    public function getBookingsProperty()
    {
        $query = Booking::with(['travelers', 'groups'])
            ->orderBy('start_date', 'desc');

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->paginate(25);
    }

    public function render()
    {
        return view('livewire.bookings-list', [
            'bookings' => $this->bookings,
        ]);
    }
}
