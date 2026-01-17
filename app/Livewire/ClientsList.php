<?php

namespace App\Livewire;

use App\Models\Traveler;
use Livewire\Component;
use Livewire\WithPagination;

class ClientsList extends Component
{
    use WithPagination;

    public $search = '';

    // View modal
    public $showViewModal = false;
    public $viewTraveler = null;

    // Edit modal
    public $showEditModal = false;
    public $editTravelerId = null;
    public $editFirstName = '';
    public $editLastName = '';
    public $editEmail = '';
    public $editPhone = '';
    public $editDob = '';
    public $editIsLead = false;

    protected $queryString = ['search'];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openViewModal(Traveler $traveler)
    {
        $this->viewTraveler = $traveler->load(['group.booking', 'payment', 'flights', 'addons', 'notes']);
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewTraveler = null;
    }

    public function openEditModal(Traveler $traveler)
    {
        $this->editTravelerId = $traveler->id;
        $this->editFirstName = $traveler->first_name;
        $this->editLastName = $traveler->last_name;
        $this->editEmail = $traveler->email ?? '';
        $this->editPhone = $traveler->phone ?? '';
        $this->editDob = $traveler->dob?->format('Y-m-d') ?? '';
        $this->editIsLead = $traveler->is_lead;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editTravelerId = null;
        $this->resetValidation();
    }

    public function updateTraveler()
    {
        $this->validate([
            'editFirstName' => 'required|string|max:255',
            'editLastName' => 'required|string|max:255',
            'editEmail' => 'nullable|email|max:255',
            'editPhone' => 'nullable|string|max:255',
            'editDob' => 'nullable|date',
        ]);

        $traveler = Traveler::findOrFail($this->editTravelerId);
        $traveler->update([
            'first_name' => $this->editFirstName,
            'last_name' => $this->editLastName,
            'email' => $this->editEmail ?: null,
            'phone' => $this->editPhone ?: null,
            'dob' => $this->editDob ?: null,
            'is_lead' => $this->editIsLead,
        ]);

        $this->closeEditModal();
        session()->flash('success', 'Client updated successfully.');
    }

    public function render()
    {
        $query = Traveler::with(['group.booking'])
            ->orderBy('last_name')
            ->orderBy('first_name');

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('group.booking', function ($bq) use ($search) {
                      $bq->where('booking_number', 'like', "%{$search}%");
                  });
            });
        }

        return view('livewire.clients-list', [
            'travelers' => $query->paginate(25),
        ]);
    }
}
