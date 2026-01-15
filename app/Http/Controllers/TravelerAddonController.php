<?php

namespace App\Http\Controllers;

use App\Models\TravelerAddon;
use App\Models\Traveler;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TravelerAddonController extends Controller
{
    public function store(Request $request, Traveler $traveler)
    {
        Gate::authorize('update', $traveler->group->booking);

        $validated = $request->validate([
            'experience_name' => 'required|string|max:255',
            'cost_per_person' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $addon = $traveler->addons()->create($validated);

        // Create ledger entry for the add-on (as a received amount to track what's owed)
        $booking = $traveler->group->booking;
        $ledgerEntry = LedgerEntry::create([
            'booking_id' => $booking->id,
            'date' => now(),
            'description' => "Add-on: {$validated['experience_name']} - {$traveler->full_name}",
            'type' => 'received',
            'received_category' => 'add_on',
            'amount' => $validated['cost_per_person'],
            'created_by' => auth()->id(),
        ]);

        $addon->update(['ledger_entry_id' => $ledgerEntry->id]);

        return redirect()->back()->with('success', 'Add-on created and synced to ledger.');
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
