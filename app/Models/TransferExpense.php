<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferExpense extends Model
{
    protected $fillable = [
        'transfer_id',
        'booking_id',
        'category',
        'vendor_name',
        'amount',
        'payment_type',
        'notes',
        'ledger_posted',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'ledger_posted' => 'boolean',
    ];

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(Transfer::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function ledgerEntry()
    {
        return $this->hasOne(LedgerEntry::class);
    }
}
