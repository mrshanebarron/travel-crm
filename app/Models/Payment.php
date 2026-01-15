<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'traveler_id',
        'safari_rate',
        'original_rate',
        'deposit',
        'deposit_locked',
        'payment_90_day',
        'payment_45_day',
    ];

    protected $casts = [
        'safari_rate' => 'decimal:2',
        'original_rate' => 'decimal:2',
        'deposit' => 'decimal:2',
        'deposit_locked' => 'boolean',
        'payment_90_day' => 'decimal:2',
        'payment_45_day' => 'decimal:2',
    ];

    /**
     * Recalculate payment schedule when rate changes.
     * Deposit stays locked, 90-day adjusts so deposit+90-day = 50%, 45-day = 50%
     */
    public function recalculateSchedule(): void
    {
        if (!$this->deposit_locked) {
            // First time setting - use standard 25/25/50
            $this->deposit = $this->safari_rate * 0.25;
            $this->payment_90_day = $this->safari_rate * 0.25;
            $this->payment_45_day = $this->safari_rate * 0.50;
            $this->original_rate = $this->safari_rate;
            $this->deposit_locked = true;
        } else {
            // Rate changed - rebalance keeping deposit fixed
            $halfTotal = $this->safari_rate * 0.50;
            $this->payment_90_day = max(0, $halfTotal - $this->deposit);
            $this->payment_45_day = $this->safari_rate * 0.50;
        }
    }

    public function traveler(): BelongsTo
    {
        return $this->belongsTo(Traveler::class);
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->deposit + $this->payment_90_day + $this->payment_45_day;
    }

    public function getBalanceAttribute(): float
    {
        return $this->safari_rate - $this->total_paid;
    }

    public function getIsFullyPaidAttribute(): bool
    {
        return $this->balance <= 0;
    }
}
