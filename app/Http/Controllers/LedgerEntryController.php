<?php

namespace App\Http\Controllers;

use App\Models\LedgerEntry;
use App\Models\Booking;
use Illuminate\Http\Request;

class LedgerEntryController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'type' => 'required|in:received,paid',
            'amount' => 'required|numeric|min:0',
        ]);

        // Calculate running balance
        $lastEntry = $booking->ledgerEntries()->orderBy('id', 'desc')->first();
        $previousBalance = $lastEntry ? $lastEntry->balance : 0;

        $newBalance = $validated['type'] === 'received'
            ? $previousBalance + $validated['amount']
            : $previousBalance - $validated['amount'];

        $booking->ledgerEntries()->create([
            ...$validated,
            'balance' => $newBalance,
            'created_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Ledger entry added successfully.');
    }

    public function destroy(LedgerEntry $ledgerEntry)
    {
        $booking = $ledgerEntry->booking;
        $ledgerEntry->delete();

        // Recalculate all balances after deletion
        $this->recalculateBalances($booking);

        return redirect()->back()->with('success', 'Ledger entry deleted successfully.');
    }

    private function recalculateBalances(Booking $booking): void
    {
        $entries = $booking->ledgerEntries()->orderBy('id')->get();
        $balance = 0;

        foreach ($entries as $entry) {
            $balance = $entry->type === 'received'
                ? $balance + $entry->amount
                : $balance - $entry->amount;

            $entry->update(['balance' => $balance]);
        }
    }
}
