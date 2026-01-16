<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Traveler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaymentController extends Controller
{
    public function store(Request $request, Traveler $traveler)
    {
        Gate::authorize('update', $traveler->group->booking);

        // Only users with modify_rates_payments permission can create payment records
        if (!auth()->user()->can('modify_rates_payments')) {
            return redirect()->back()->with('error', 'You do not have permission to modify rates and payments.');
        }

        $validated = $request->validate([
            'safari_rate' => 'required|numeric|min:0',
        ]);

        // Calculate days until safari for booking timing logic
        $booking = $traveler->group->booking;
        $daysUntilSafari = now()->startOfDay()->diffInDays($booking->start_date, false);

        $payment = new Payment();
        $payment->traveler_id = $traveler->id;
        $payment->safari_rate = $validated['safari_rate'];
        $payment->recalculateSchedule($daysUntilSafari);
        $payment->save();

        return redirect()->route('bookings.show', ['booking' => $booking->id, 'tab' => 'payment-details'])
            ->with('success', 'Payment record created successfully.');
    }

    public function update(Request $request, Payment $payment)
    {
        Gate::authorize('update', $payment->traveler->group->booking);

        // Only users with modify_rates_payments permission can update payment records
        if (!auth()->user()->can('modify_rates_payments')) {
            return redirect()->back()->with('error', 'You do not have permission to modify rates and payments.');
        }

        // Only super admins can modify an existing safari rate that's already locked
        if ($payment->deposit_locked && !auth()->user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Only super administrators can modify locked safari rates.');
        }

        $validated = $request->validate([
            'safari_rate' => 'required|numeric|min:0',
        ]);

        $payment->safari_rate = $validated['safari_rate'];
        $payment->recalculateSchedule();
        $payment->save();

        $booking = $payment->traveler->group->booking;

        return redirect()->route('bookings.show', ['booking' => $booking->id, 'tab' => 'payment-details'])
            ->with('success', 'Payment record updated successfully.');
    }

    /**
     * Toggle payment status (paid/unpaid) for a specific payment field.
     */
    public function togglePaid(Request $request, Payment $payment)
    {
        Gate::authorize('update', $payment->traveler->group->booking);

        if (!auth()->user()->can('modify_rates_payments')) {
            return redirect()->back()->with('error', 'You do not have permission to modify rates and payments.');
        }

        $validated = $request->validate([
            'field' => 'required|in:deposit,payment_90_day,payment_45_day',
        ]);

        $paidField = $validated['field'] . '_paid';
        $payment->$paidField = !$payment->$paidField;
        $payment->save();

        $booking = $payment->traveler->group->booking;

        return redirect()->route('bookings.show', ['booking' => $booking->id, 'tab' => 'payment-details'])
            ->with('success', 'Payment status updated.');
    }
}
