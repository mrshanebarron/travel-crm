<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Traveler;
use Livewire\Component;

class BookingPayments extends Component
{
    public Booking $booking;

    // For inline editing
    public $editingPaymentId = null;
    public $editingSafariRate = '';

    protected $listeners = ['paymentUpdated' => '$refresh', 'addonUpdated' => '$refresh'];

    public function mount(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function startEditing($paymentId, $currentRate)
    {
        $this->editingPaymentId = $paymentId;
        $this->editingSafariRate = $currentRate;
    }

    public function cancelEditing()
    {
        $this->editingPaymentId = null;
        $this->editingSafariRate = '';
    }

    public function updateSafariRate($paymentId)
    {
        if (!auth()->user()->can('modify_rates_payments')) {
            session()->flash('error', 'You do not have permission to modify rates and payments.');
            return;
        }

        $payment = Payment::findOrFail($paymentId);

        // Only super admins can modify locked rates
        if ($payment->deposit_locked && !auth()->user()->isSuperAdmin()) {
            session()->flash('error', 'Only super administrators can modify locked safari rates.');
            return;
        }

        $this->validate([
            'editingSafariRate' => 'required|numeric|min:0',
        ]);

        $oldRate = $payment->safari_rate;
        $oldDeposit = $payment->deposit;
        $old90Day = $payment->payment_90_day;
        $old45Day = $payment->payment_45_day;

        $payment->safari_rate = $this->editingSafariRate;

        // Get days until safari for proper recalculation
        $daysUntilSafari = now()->startOfDay()->diffInDays($this->booking->start_date, false);
        $payment->recalculateSchedule($daysUntilSafari);
        $payment->save();

        // Log rate change
        if ($oldRate != $this->editingSafariRate) {
            $traveler = $payment->traveler;
            $notes = "Safari rate changed for {$traveler->full_name}: \${$oldRate} → \${$this->editingSafariRate}";
            if ($oldDeposit != $payment->deposit) {
                $notes .= " | Deposit: \${$oldDeposit} → \${$payment->deposit}";
            }
            if ($old90Day != $payment->payment_90_day) {
                $notes .= " | 90-Day: \${$old90Day} → \${$payment->payment_90_day}";
            }
            if ($old45Day != $payment->payment_45_day) {
                $notes .= " | 45-Day: \${$old45Day} → \${$payment->payment_45_day}";
            }
            $this->booking->activityLogs()->create([
                'user_id' => auth()->id(),
                'action_type' => 'rate_changed',
                'notes' => $notes,
            ]);
            $this->dispatch('activityCreated');
        }

        $this->cancelEditing();
        $this->dispatch('paymentUpdated');
        $this->booking->refresh();
        session()->flash('success', 'Safari rate updated successfully.');
    }

    public function createPayment($travelerId, $safariRate)
    {
        if (!auth()->user()->can('modify_rates_payments')) {
            session()->flash('error', 'You do not have permission to modify rates and payments.');
            return;
        }

        $traveler = Traveler::findOrFail($travelerId);
        $daysUntilSafari = now()->startOfDay()->diffInDays($this->booking->start_date, false);

        $payment = new Payment();
        $payment->traveler_id = $traveler->id;
        $payment->safari_rate = $safariRate;
        $payment->recalculateSchedule($daysUntilSafari);
        $payment->save();

        $this->dispatch('paymentUpdated');
        $this->booking->refresh();
        session()->flash('success', 'Payment record created successfully.');
    }

    public function togglePaid($paymentId, $field)
    {
        if (!auth()->user()->can('modify_rates_payments')) {
            session()->flash('error', 'You do not have permission to modify rates and payments.');
            return;
        }

        $payment = Payment::findOrFail($paymentId);
        $paidField = $field . '_paid';
        $payment->$paidField = !$payment->$paidField;
        $payment->save();

        $this->dispatch('paymentUpdated');
        $this->booking->refresh();
        session()->flash('success', 'Payment status updated.');
    }

    public function render()
    {
        // Reload booking with all relationships
        $this->booking->load(['groups.travelers.payment', 'groups.travelers.addons']);

        return view('livewire.booking-payments');
    }
}
