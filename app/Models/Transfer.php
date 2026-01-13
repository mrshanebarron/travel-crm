<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transfer extends Model
{
    protected $fillable = [
        'transfer_number',
        'request_date',
        'status',
        'total_amount',
        'created_by',
        'transfer_task_id',
        'vendor_task_id',
        'sent_at',
        'transfer_completed_at',
        'vendor_payments_completed_at',
    ];

    protected $casts = [
        'request_date' => 'date',
        'total_amount' => 'decimal:2',
        'sent_at' => 'datetime',
        'transfer_completed_at' => 'datetime',
        'vendor_payments_completed_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transferTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'transfer_task_id');
    }

    public function vendorTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'vendor_task_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(TransferExpense::class);
    }

    public function recalculateTotal(): void
    {
        $this->update([
            'total_amount' => $this->expenses()->sum('amount'),
        ]);
    }

    public static function generateTransferNumber(): string
    {
        $year = date('Y');
        $prefix = 'TR-' . $year . '-';
        $lastTransfer = self::where('transfer_number', 'like', $prefix . '%')
            ->orderBy('transfer_number', 'desc')
            ->first();

        if ($lastTransfer) {
            $lastNumber = (int) substr($lastTransfer->transfer_number, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
