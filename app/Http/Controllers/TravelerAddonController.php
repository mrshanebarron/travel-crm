<?php

namespace App\Http\Controllers;

use App\Models\TravelerAddon;
use App\Models\Traveler;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TravelerAddonController extends Controller
{
    public function store(Request $request, Traveler $traveler)
    {
        Gate::authorize('update', $traveler->group->booking);

        $validated = $request->validate([
            'type' => 'nullable|in:add_on,credit',
            'experience_name' => 'required|string|max:255',
            'cost_per_person' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $type = $validated['type'] ?? 'add_on';
        $isCredit = $type === 'credit';

        try {
            DB::transaction(function () use ($traveler, $validated, $type, $isCredit) {
                $booking = $traveler->group->booking;

                // For credits, the ledger entry type is 'paid' (money going out/refund)
                // For add-ons, it's 'received' (money owed to us)
                $ledgerEntry = LedgerEntry::create([
                    'booking_id' => $booking->id,
                    'date' => now(),
                    'description' => ($isCredit ? "Credit: " : "Add-on: ") . "{$validated['experience_name']} - {$traveler->full_name}",
                    'type' => $isCredit ? 'paid' : 'received',
                    'received_category' => $isCredit ? null : 'add_on',
                    'paid_category' => $isCredit ? 'credit' : null,
                    'amount' => $validated['cost_per_person'],
                    'created_by' => auth()->id(),
                ]);

                // Create addon with ledger_entry_id in single operation
                $traveler->addons()->create([
                    'type' => $type,
                    'experience_name' => $validated['experience_name'],
                    'cost_per_person' => $validated['cost_per_person'],
                    'notes' => $validated['notes'] ?? null,
                    'ledger_entry_id' => $ledgerEntry->id,
                ]);
            });

            $message = $isCredit ? 'Credit applied and synced to ledger.' : 'Add-on created and synced to ledger.';
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create: ' . $e->getMessage());
        }
    }

    public function markPaid(TravelerAddon $addon)
    {
        Gate::authorize('update', $addon->traveler->group->booking);

        $addon->update(['paid' => true]);

        return redirect()->back()->with('success', 'Add-on marked as paid.');
    }

    public function destroy(TravelerAddon $addon)
    {
        Gate::authorize('update', $addon->traveler->group->booking);

        // Delete associated ledger entry if exists
        if ($addon->ledger_entry_id) {
            LedgerEntry::where('id', $addon->ledger_entry_id)->delete();
        }

        $addon->delete();

        return redirect()->back()->with('success', 'Add-on deleted.');
    }
}
