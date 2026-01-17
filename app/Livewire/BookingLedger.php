<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\LedgerEntry;
use Livewire\Component;

class BookingLedger extends Component
{
    public Booking $booking;

    // Form fields
    public $date;
    public $type = 'received';
    public $receivedCategory = 'deposit';
    public $paidCategory = 'lodges_camps';
    public $vendorName = '';
    public $amount = '';
    public $description = '';

    protected $listeners = ['ledgerUpdated' => '$refresh', 'paymentUpdated' => '$refresh'];

    protected $rules = [
        'date' => 'required|date',
        'type' => 'required|in:received,paid',
        'amount' => 'required|numeric|min:0.01',
        'description' => 'nullable|string|max:255',
    ];

    public function mount(Booking $booking)
    {
        $this->booking = $booking;
        $this->date = date('Y-m-d');
    }

    public function addEntry()
    {
        $this->validate();

        // Build description based on type
        $fullDescription = $this->description;
        if ($this->type === 'received') {
            $categoryLabels = [
                'deposit' => 'Deposit (25%)',
                '90_day' => '90-Day Payment (25%)',
                '45_day' => '45-Day Payment (50%)',
                'other' => 'Other Payment',
            ];
            $fullDescription = $categoryLabels[$this->receivedCategory] ?? $this->receivedCategory;
            if ($this->description) {
                $fullDescription .= ' - ' . $this->description;
            }
        } else {
            $categoryLabels = [
                'lodges_camps' => 'Lodges/Camps',
                'driver_guide' => 'Driver/Guide',
                'park_entry' => 'Park Entry',
                'arrival_dept_flight' => 'Arrival/Dept Flight',
                'internal_flights' => 'Internal Flights',
                'driver_guide_invoices' => 'Driver/Guide Invoices',
                'misc' => 'Misc',
            ];
            $fullDescription = $categoryLabels[$this->paidCategory] ?? $this->paidCategory;
            if ($this->vendorName) {
                $fullDescription .= ' - ' . $this->vendorName;
            }
            if ($this->description) {
                $fullDescription .= ' - ' . $this->description;
            }
        }

        // Calculate running balance
        $previousBalance = $this->booking->ledgerEntries()->latest('date')->value('balance') ?? 0;
        $balance = $this->type === 'received'
            ? $previousBalance + $this->amount
            : $previousBalance - $this->amount;

        $this->booking->ledgerEntries()->create([
            'date' => $this->date,
            'type' => $this->type,
            'amount' => $this->amount,
            'description' => $fullDescription,
            'balance' => $balance,
        ]);
        $this->dispatch('ledgerUpdated');

        $this->reset(['amount', 'description', 'vendorName']);
        $this->type = 'received';
        $this->receivedCategory = 'deposit';
        $this->paidCategory = 'lodges_camps';
        $this->date = date('Y-m-d');
        $this->booking->refresh();
    }

    public function deleteEntry(LedgerEntry $entry)
    {
        $entry->delete();

        // Recalculate balances
        $entries = $this->booking->ledgerEntries()->orderBy('date')->get();
        $balance = 0;
        foreach ($entries as $e) {
            $balance = $e->type === 'received' ? $balance + $e->amount : $balance - $e->amount;
            $e->update(['balance' => $balance]);
        }

        $this->dispatch('ledgerUpdated');
        $this->booking->refresh();
    }

    public function render()
    {
        $entries = $this->booking->ledgerEntries()->orderBy('date')->get();
        $totalReceived = $entries->where('type', 'received')->sum('amount');
        $totalPaid = $entries->where('type', 'paid')->sum('amount');
        $balance = $totalReceived - $totalPaid;

        return view('livewire.booking-ledger', [
            'entries' => $entries,
            'totalReceived' => $totalReceived,
            'totalPaid' => $totalPaid,
            'balance' => $balance,
        ]);
    }
}
