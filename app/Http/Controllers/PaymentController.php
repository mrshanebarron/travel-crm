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

        $validated = $request->validate([
            'safari_rate' => 'required|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'payment_90_day' => 'nullable|numeric|min:0',
            'payment_45_day' => 'nullable|numeric|min:0',
        ]);

        $traveler->payment()->create($validated);

        return redirect()->back()->with('success', 'Payment record created successfully.');
    }

    public function update(Request $request, Payment $payment)
    {
        Gate::authorize('update', $payment->traveler->group->booking);

        $validated = $request->validate([
            'safari_rate' => 'required|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'payment_90_day' => 'nullable|numeric|min:0',
            'payment_45_day' => 'nullable|numeric|min:0',
        ]);

        $payment->update($validated);

        return redirect()->back()->with('success', 'Payment record updated successfully.');
    }
}
