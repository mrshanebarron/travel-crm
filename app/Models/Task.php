<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use App\Models\LedgerEntry;

class Task extends Model
{
    protected $fillable = [
        'booking_id',
        'transfer_id',
        'name',
        'description',
        'assigned_to',
        'assigned_at',
        'assigned_by',
        'status',
        'due_date',
        'days_before_safari',
        'timing_description',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(Transfer::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function markComplete(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // If this is a transfer task, trigger the transfer workflow
        if ($this->transfer_id) {
            $this->handleTransferTaskCompletion();
        }
    }

    /**
     * Handle transfer workflow when a transfer-related task is completed.
     */
    protected function handleTransferTaskCompletion(): void
    {
        $transfer = $this->transfer;
        if (!$transfer) {
            return;
        }

        // Check if this is the "Make Transfer" task (transfer_task_id)
        if ($transfer->transfer_task_id === $this->id && $transfer->status === 'sent') {
            // Advance transfer to transfer_completed and create Hilda's task
            $transfer->status = 'transfer_completed';
            $transfer->transfer_completed_at = now();

            // Create Task for Hilda to make vendor payments
            $hildaUserId = User::where('email', 'hilda@tapestryofafrica.com')->value('id') ?? 3;
            $vendorTask = self::create([
                'name' => "Transfer completed – make vendor payments ({$transfer->transfer_number})",
                'description' => "Transfer Amount: $" . number_format($transfer->total_amount, 2) . "\n\nVendor Payments Required:\n" . $this->formatExpensesForTask($transfer),
                'status' => 'pending',
                'due_date' => now(),
                'assigned_to' => $hildaUserId,
                'assigned_at' => now(),
                'assigned_by' => auth()->id() ?? 1,
                'transfer_id' => $transfer->id,
            ]);

            $transfer->vendor_task_id = $vendorTask->id;
            $transfer->save();
        }
        // Check if this is the "Make Vendor Payments" task (vendor_task_id)
        elseif ($transfer->vendor_task_id === $this->id && $transfer->status === 'transfer_completed') {
            // Advance transfer to vendor_payments_completed and post ledger entries
            $transfer->status = 'vendor_payments_completed';
            $transfer->vendor_payments_completed_at = now();
            $transfer->save();

            // Auto-post ledger entries
            DB::transaction(function () use ($transfer) {
                foreach ($transfer->expenses as $expense) {
                    if ($expense->booking_id) {
                        // Check if ledger entry already exists
                        $existingEntry = LedgerEntry::where('booking_id', $expense->booking_id)
                            ->where('description', 'LIKE', '%Transfer ' . $transfer->transfer_number . '%')
                            ->first();

                        if (!$existingEntry) {
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
                                'created_by' => auth()->id() ?? 1,
                            ]);

                            $expense->update(['ledger_posted' => true]);
                        }
                    }
                }
            });
        }
    }

    /**
     * Format expenses for task description.
     */
    protected function formatExpensesForTask(Transfer $transfer): string
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
