<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\LedgerEntry;
use App\Models\Booking;
use Illuminate\Http\Request;

class LedgerEntryController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:received,paid',
            'amount' => 'required|numeric|min:0',
            'received_category' => 'nullable|string|max:50',
            'paid_category' => 'nullable|string|max:50',
            'vendor_name' => 'nullable|string|max:255',
        ]);

        // Build description from category and vendor if not provided
        if (empty($validated['description'])) {
            if ($validated['type'] === 'received') {
                $categoryLabels = [
                    'deposit' => 'Deposit (25%)',
                    '90_day' => '90-Day Payment (25%)',
                    '45_day' => '45-Day Payment (50%)',
                    'other' => 'Other Payment',
                ];
                $validated['description'] = $categoryLabels[$validated['received_category'] ?? 'other'] ?? 'Payment Received';
            } else {
                $categoryLabels = [
                    'lodge' => 'Lodge/Camp',
                    'transport' => 'Transport',
                    'flights' => 'Internal Flights',
                    'park_fees' => 'Park Fees',
                    'guide' => 'Safari Guide',
                    'meals' => 'Meals',
                    'other' => 'Other Expense',
                ];
                $categoryLabel = $categoryLabels[$validated['paid_category'] ?? 'other'] ?? 'Expense';
                $vendor = $validated['vendor_name'] ?? '';
                $validated['description'] = $vendor ? "{$categoryLabel} - {$vendor}" : $categoryLabel;
            }
        }

        // Calculate running balance
        $lastEntry = $booking->ledgerEntries()->orderBy('id', 'desc')->first();
        $previousBalance = $lastEntry ? $lastEntry->balance : 0;

        $newBalance = $validated['type'] === 'received'
            ? $previousBalance + $validated['amount']
            : $previousBalance - $validated['amount'];

        $entry = $booking->ledgerEntries()->create([
            ...$validated,
            'balance' => $newBalance,
            'created_by' => auth()->id(),
        ]);

        $actionType = $validated['type'] === 'received' ? 'payment_received' : 'ledger_entry';
        ActivityLog::logAction(
            $booking->id,
            $actionType,
            "{$validated['description']} - \${$validated['amount']}",
            'LedgerEntry',
            $entry->id
        );

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
