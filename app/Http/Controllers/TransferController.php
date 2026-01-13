<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Transfer;
use App\Models\TransferExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function index(Request $request)
    {
        $query = Transfer::with(['creator', 'expenses.booking']);

        // Filter by status if provided
        if ($request->has('status') && in_array($request->status, ['draft', 'sent', 'transfer_completed', 'vendor_payments_completed'])) {
            $query->where('status', $request->status);
        }

        $transfers = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('transfers.index', compact('transfers'));
    }

    public function create()
    {
        return view('transfers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_date' => 'required|date',
        ]);

        $transfer = Transfer::create([
            'transfer_number' => Transfer::generateTransferNumber(),
            'request_date' => $validated['request_date'],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('transfers.edit', $transfer)
            ->with('success', 'Transfer request created. Add expenses below.');
    }

    public function show(Transfer $transfer)
    {
        $transfer->load(['expenses.booking', 'creator', 'transferTask', 'vendorTask']);
        return view('transfers.show', compact('transfer'));
    }

    public function edit(Transfer $transfer)
    {
        $transfer->load(['expenses.booking']);
        $bookings = Booking::where('status', '!=', 'completed')->orderBy('booking_number')->get();
        return view('transfers.edit', compact('transfer', 'bookings'));
    }

    public function update(Request $request, Transfer $transfer)
    {
        $validated = $request->validate([
            'request_date' => 'required|date',
            'status' => 'required|in:draft,sent,transfer_completed,vendor_payments_completed',
        ]);

        $updateData = [
            'request_date' => $validated['request_date'],
            'status' => $validated['status'],
        ];

        // Set timestamps based on status changes
        if ($validated['status'] === 'sent' && !$transfer->sent_at) {
            $updateData['sent_at'] = now();
        }
        if ($validated['status'] === 'transfer_completed' && !$transfer->transfer_completed_at) {
            $updateData['transfer_completed_at'] = now();
        }
        if ($validated['status'] === 'vendor_payments_completed' && !$transfer->vendor_payments_completed_at) {
            $updateData['vendor_payments_completed_at'] = now();
        }

        $transfer->update($updateData);

        return redirect()->route('transfers.show', $transfer)
            ->with('success', 'Transfer request updated.');
    }

    public function destroy(Transfer $transfer)
    {
        $transfer->delete();

        return redirect()->route('transfers.index')
            ->with('success', 'Transfer request deleted.');
    }
}
