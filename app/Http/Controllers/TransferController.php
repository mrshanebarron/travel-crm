<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Transfer;
use App\Models\TransferExpense;
use App\Models\LedgerEntry;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Transfer::class);

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
        $this->authorize('create', Transfer::class);
        return view('transfers.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Transfer::class);

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
        $this->authorize('view', $transfer);
        $transfer->load(['expenses.booking', 'creator', 'transferTask', 'vendorTask']);
        return view('transfers.show', compact('transfer'));
    }

    public function edit(Transfer $transfer)
    {
        $this->authorize('update', $transfer);
        $transfer->load(['expenses.booking']);
        $bookings = Booking::with(['groups.travelers'])
            ->where('status', '!=', 'completed')
            ->orderBy('start_date')
            ->get();
        return view('transfers.edit', compact('transfer', 'bookings'));
    }

    public function update(Request $request, Transfer $transfer)
    {
        $this->authorize('update', $transfer);

        $validated = $request->validate([
            'request_date' => 'required|date',
            'status' => 'required|in:draft,sent,transfer_completed,vendor_payments_completed',
        ]);

        $updateData = [
            'request_date' => $validated['request_date'],
            'status' => $validated['status'],
        ];

        $oldStatus = $transfer->status;

        // Set timestamps and create tasks based on status changes
        // Note: draft→sent transition is now primarily handled by send() method,
        // but keep this for backward compatibility or manual status changes
        if ($validated['status'] === 'sent' && $oldStatus === 'draft') {
            $updateData['sent_at'] = now();

            // Create Task 1: Transfer Execution - assigned to Matt
            $mattUserId = User::where('email', 'matt@tapestryofafrica.com')->value('id') ?? 1;
            $transferTask = Task::create([
                'name' => "Make transfer for {$transfer->transfer_number} ({$transfer->request_date->format('M j, Y')})",
                'description' => "Transfer Amount: $" . number_format($transfer->total_amount, 2) . "\n\nExpenses:\n" . $this->formatExpensesForTask($transfer),
                'status' => 'pending',
                'due_date' => now(),
                'assigned_to' => $mattUserId,
                'assigned_at' => now(),
                'assigned_by' => auth()->id(),
                'transfer_id' => $transfer->id,
            ]);

            $updateData['transfer_task_id'] = $transferTask->id;
        }

        if ($validated['status'] === 'transfer_completed' && $oldStatus === 'sent') {
            $updateData['transfer_completed_at'] = now();

            // Mark Task 1 as completed
            if ($transfer->transferTask) {
                $transfer->transferTask->markComplete();
            }

            // Create Task 2: Vendor Payments - assigned to Hilda
            $hildaUserId = User::where('email', 'hilda@tapestryofafrica.com')->value('id') ?? 3;
            $vendorTask = Task::create([
                'name' => "Transfer completed – make vendor payments ({$transfer->transfer_number})",
                'description' => "Transfer Amount: $" . number_format($transfer->total_amount, 2) . "\n\nVendor Payments Required:\n" . $this->formatExpensesForTask($transfer),
                'status' => 'pending',
                'due_date' => now(),
                'assigned_to' => $hildaUserId,
                'assigned_at' => now(),
                'assigned_by' => auth()->id(),
                'transfer_id' => $transfer->id,
            ]);

            $updateData['vendor_task_id'] = $vendorTask->id;
        }

        if ($validated['status'] === 'vendor_payments_completed' && $oldStatus === 'transfer_completed') {
            $updateData['vendor_payments_completed_at'] = now();

            // Mark Task 2 as completed
            if ($transfer->vendorTask) {
                $transfer->vendorTask->markComplete();
            }

            // Auto-post ledger entries when vendor payments are completed
            // This creates "paid" entries in each booking's ledger
            DB::transaction(function () use ($transfer) {
                foreach ($transfer->expenses as $expense) {
                    if ($expense->booking_id) {
                        // Check if ledger entry already exists for this transfer expense
                        $existingEntry = LedgerEntry::where('booking_id', $expense->booking_id)
                            ->where('description', 'LIKE', '%Transfer ' . $transfer->transfer_number . '%')
                            ->first();

                        if (!$existingEntry) {
                            // Calculate running balance for this booking
                            $lastEntry = LedgerEntry::where('booking_id', $expense->booking_id)
                                ->orderBy('id', 'desc')
                                ->first();

                            $balance = ($lastEntry ? $lastEntry->balance : 0) - $expense->amount;

                            LedgerEntry::create([
                                'booking_id' => $expense->booking_id,
                                'date' => $transfer->request_date,
                                'description' => 'Transfer ' . $transfer->transfer_number . ' - ' . $expense->description,
                                'type' => 'paid',
                                'amount' => $expense->amount,
                                'balance' => $balance,
                                'transfer_expense_id' => $expense->id,
                            ]);

                            // Mark expense as ledger posted
                            $expense->update(['ledger_posted' => true]);
                        }
                    }
                }
            });
        }

        $transfer->update($updateData);

        $message = 'Transfer request updated.';
        if ($oldStatus !== 'vendor_payments_completed' && $validated['status'] === 'vendor_payments_completed') {
            $message = 'Transfer completed! Ledger entries have been posted to each booking.';
        }

        return redirect()->route('transfers.show', $transfer)
            ->with('success', $message);
    }

    /**
     * Send a draft transfer - changes status to 'sent' and creates task.
     */
    public function send(Transfer $transfer)
    {
        $this->authorize('update', $transfer);

        if ($transfer->status !== 'draft') {
            return redirect()->route('transfers.show', $transfer)
                ->with('error', 'Only draft transfers can be sent.');
        }

        if ($transfer->expenses->count() === 0) {
            return redirect()->route('transfers.edit', $transfer)
                ->with('error', 'Cannot send a transfer with no expenses. Add expenses first.');
        }

        // Update status to sent
        $transfer->status = 'sent';
        $transfer->sent_at = now();

        // Create Task: Transfer Execution - assigned to Matt (user ID 1)
        // Note: In production, this should be configurable or use a role-based lookup
        $mattUserId = User::where('email', 'matt@tapestryofafrica.com')->value('id') ?? 1;

        $transferTask = Task::create([
            'name' => "Make transfer for {$transfer->transfer_number} ({$transfer->request_date->format('M j, Y')})",
            'description' => "Transfer Amount: $" . number_format($transfer->total_amount, 2) . "\n\nExpenses:\n" . $this->formatExpensesForTask($transfer),
            'status' => 'pending',
            'due_date' => now(),  // Due today since it should appear immediately
            'assigned_to' => $mattUserId,
            'assigned_at' => now(),
            'assigned_by' => auth()->id(),
            'transfer_id' => $transfer->id,  // Link to transfer (no booking_id needed)
        ]);

        $transfer->transfer_task_id = $transferTask->id;
        $transfer->save();

        return redirect()->route('transfers.show', $transfer)
            ->with('success', 'Transfer sent! A task has been created to make the transfer.');
    }

    public function destroy(Transfer $transfer)
    {
        $this->authorize('delete', $transfer);
        $transfer->delete();

        return redirect()->route('transfers.index')
            ->with('success', 'Transfer request deleted.');
    }

    /**
     * Format expenses for task description.
     */
    private function formatExpensesForTask(Transfer $transfer): string
    {
        $lines = [];
        foreach ($transfer->expenses as $expense) {
            $booking = $expense->booking;
            $leadTraveler = $booking ? $booking->leadTraveler() : null;
            $bookingRef = $leadTraveler
                ? "{$leadTraveler->last_name}, {$leadTraveler->first_name}"
                : ($booking ? $booking->booking_number : 'Unknown');

            $lines[] = "- {$expense->description}: $" . number_format($expense->amount, 2) . " ({$bookingRef})";
        }
        return implode("\n", $lines);
    }
}
