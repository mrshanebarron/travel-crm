<?php

namespace App\Http\Controllers;

use App\Models\TransferExpense;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TransferExpenseController extends Controller
{
    public function store(Request $request, Transfer $transfer)
    {
        Gate::authorize('update', $transfer);

        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'category' => 'required|in:lodge,guide_vehicle,park_entry,misc',
            'vendor_name' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:deposit,final,other',
            'notes' => 'nullable|string',
        ]);

        $transfer->expenses()->create($validated);
        $transfer->recalculateTotal();

        return redirect()->back()->with('success', 'Expense added successfully.');
    }

    public function update(Request $request, TransferExpense $transferExpense)
    {
        Gate::authorize('update', $transferExpense->transfer);

        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'category' => 'required|in:lodge,guide_vehicle,park_entry,misc',
            'vendor_name' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:deposit,final,other',
            'notes' => 'nullable|string',
        ]);

        $transferExpense->update($validated);
        $transferExpense->transfer->recalculateTotal();

        return redirect()->back()->with('success', 'Expense updated successfully.');
    }

    public function destroy(TransferExpense $transferExpense)
    {
        Gate::authorize('update', $transferExpense->transfer);

        $transfer = $transferExpense->transfer;
        $transferExpense->delete();
        $transfer->recalculateTotal();

        return redirect()->back()->with('success', 'Expense removed successfully.');
    }
}
