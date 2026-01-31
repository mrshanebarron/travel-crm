<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\Group;
use App\Models\Traveler;
use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;

class BookingsList extends Component
{
    use WithPagination;

    public $status = 'upcoming';
    public $selected = [];
    public $selectAll = false;

    // Create booking modal
    public $showCreateModal = false;
    public $country = '';
    public $startDate = '';
    public $endDate = '';
    public $travelers = [];

    protected $queryString = [
        'status' => ['except' => 'upcoming']
    ];

    public function mount()
    {
        // Get status from URL or default to 'upcoming'
        $this->status = request()->get('status', 'upcoming');
        
        $this->travelers = [['first_name' => '', 'last_name' => '', 'email' => '', 'phone' => '', 'dob' => '']];
    }

    public function openCreateModal()
    {
        $this->reset(['country', 'startDate', 'endDate']);
        $this->travelers = [['first_name' => '', 'last_name' => '', 'email' => '', 'phone' => '', 'dob' => '']];
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function addTraveler()
    {
        $this->travelers[] = ['first_name' => '', 'last_name' => '', 'email' => '', 'phone' => '', 'dob' => ''];
    }

    public function removeTraveler($index)
    {
        if (count($this->travelers) > 1) {
            unset($this->travelers[$index]);
            $this->travelers = array_values($this->travelers);
        }
    }

    public function createBooking()
    {
        $this->validate([
            'country' => 'required|string',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'travelers.0.first_name' => 'required|string|max:255',
            'travelers.0.last_name' => 'required|string|max:255',
        ], [
            'travelers.0.first_name.required' => 'Lead traveler first name is required.',
            'travelers.0.last_name.required' => 'Lead traveler last name is required.',
        ]);

        // Create booking with generated booking number
        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'country' => $this->country,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'status' => 'upcoming',
            'created_by' => auth()->id(),
        ]);

        // Create default group
        $group = Group::create([
            'booking_id' => $booking->id,
            'group_number' => 1,
        ]);

        // Create travelers
        foreach ($this->travelers as $index => $travelerData) {
            if (empty($travelerData['first_name']) && empty($travelerData['last_name'])) {
                continue;
            }

            Traveler::create([
                'group_id' => $group->id,
                'first_name' => $travelerData['first_name'],
                'last_name' => $travelerData['last_name'],
                'email' => $travelerData['email'] ?: null,
                'phone' => $travelerData['phone'] ?: null,
                'dob' => $travelerData['dob'] ?: null,
                'is_lead' => $index === 0,
                'order' => $index + 1,
            ]);
        }

        // Create default tasks
        app(\App\Http\Controllers\BookingController::class)->createDefaultTasks($booking);

        $this->closeCreateModal();
        session()->flash('success', 'Booking created successfully.');

        return redirect()->route('bookings.show', $booking);
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
        $query = Booking::with(['travelers', 'groups']);

        // Get the current status, defaulting to 'upcoming'
        $status = $this->status ?: 'upcoming';

        // Apply status filter unless showing all
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Sort by start date - upcoming first for 'upcoming' status
        if ($status === 'upcoming') {
            $query->orderBy('start_date', 'asc');
        } else {
            $query->orderBy('start_date', 'desc');
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
