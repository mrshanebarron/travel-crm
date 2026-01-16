<?php

namespace App\Livewire;

use App\Models\Vendor;
use Livewire\Component;
use Livewire\WithPagination;

class VendorsList extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';

    // Create vendor modal
    public $showCreateModal = false;
    public $name = '';
    public $vendorCategory = '';
    public $country = '';
    public $contactName = '';
    public $email = '';
    public $phone = '';
    public $whatsapp = '';
    public $address = '';
    public $bankName = '';
    public $bankAccount = '';
    public $swiftCode = '';
    public $paymentTerms = '';
    public $notes = '';
    public $isActive = true;

    protected $queryString = ['search', 'category'];

    public function mount()
    {
        $this->search = request('search', '');
        $this->category = request('category', '');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategory()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->category = '';
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset([
            'name', 'vendorCategory', 'country', 'contactName', 'email',
            'phone', 'whatsapp', 'address', 'bankName', 'bankAccount',
            'swiftCode', 'paymentTerms', 'notes'
        ]);
        $this->isActive = true;
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function createVendor()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'vendorCategory' => 'required|string',
        ], [
            'name.required' => 'Vendor name is required.',
            'vendorCategory.required' => 'Category is required.',
        ]);

        $vendor = Vendor::create([
            'name' => $this->name,
            'category' => $this->vendorCategory,
            'country' => $this->country ?: null,
            'contact_name' => $this->contactName ?: null,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'whatsapp' => $this->whatsapp ?: null,
            'address' => $this->address ?: null,
            'bank_name' => $this->bankName ?: null,
            'bank_account' => $this->bankAccount ?: null,
            'swift_code' => $this->swiftCode ?: null,
            'payment_terms' => $this->paymentTerms ?: null,
            'notes' => $this->notes ?: null,
            'is_active' => $this->isActive,
        ]);

        $this->closeCreateModal();
        session()->flash('success', 'Vendor created successfully.');

        return redirect()->route('vendors.show', $vendor);
    }

    public function getVendorsProperty()
    {
        $query = Vendor::query()->orderBy('name');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('contact_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->category) {
            $query->where('category', $this->category);
        }

        return $query->paginate(25);
    }

    public function render()
    {
        return view('livewire.vendors-list', [
            'vendors' => $this->vendors,
            'categories' => Vendor::CATEGORIES,
        ]);
    }
}
