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

    /**
     * Get a formatted description for ledger entries.
     */
    public function getDescriptionAttribute(): string
    {
        $categoryLabels = [
            'lodge' => 'Lodge/Camp',
            'guide_vehicle' => 'Guide/Vehicle',
            'park_entry' => 'Park Entry & Activities',
            'misc' => 'Miscellaneous',
        ];

        $category = $categoryLabels[$this->category] ?? ucfirst($this->category);
        $vendor = $this->vendor_name ? " - {$this->vendor_name}" : '';
        $paymentType = $this->payment_type ? " ({$this->payment_type})" : '';

        return "{$category}{$vendor}{$paymentType}";
    }
}
