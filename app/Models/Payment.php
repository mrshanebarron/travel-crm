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
     *
     * Booking Timing Logic:
     * - Normal booking (> 90 days out): 25% deposit, 25% at 90-day, 50% at 45-day
     * - Within 90 days: 50% deposit (skip 90-day payment), 50% at 45-day
     * - Within 45 days: 100% due immediately
     */
    public function recalculateSchedule(?int $daysUntilSafari = null): void
    {
        if (!$this->deposit_locked) {
            // First time setting - determine schedule based on booking timing
            if ($daysUntilSafari !== null && $daysUntilSafari <= 45) {
                // Within 45 days - 100% due immediately
                $this->deposit = $this->safari_rate;
                $this->payment_90_day = 0;
                $this->payment_45_day = 0;
            } elseif ($daysUntilSafari !== null && $daysUntilSafari <= 90) {
                // Within 90 days - 50% deposit, skip 90-day, 50% at 45-day
                $this->deposit = $this->safari_rate * 0.50;
                $this->payment_90_day = 0;
                $this->payment_45_day = $this->safari_rate * 0.50;
            } else {
                // Standard booking - 25/25/50 split
                $this->deposit = $this->safari_rate * 0.25;
                $this->payment_90_day = $this->safari_rate * 0.25;
                $this->payment_45_day = $this->safari_rate * 0.50;
            }
            $this->original_rate = $this->safari_rate;
            $this->deposit_locked = true;
        } else {
            // Rate changed - rebalance keeping deposit fixed
            // Total must always equal safari_rate
            $remainingAfterDeposit = $this->safari_rate - $this->deposit;

            // If 90-day was 0 (late booking), keep it 0 and put remainder in 45-day
            if ($this->payment_90_day == 0) {
                $this->payment_45_day = max(0, $remainingAfterDeposit);
            } else {
                // Normal booking: try deposit + 90-day = 50%, 45-day = 50%
                $halfTotal = $this->safari_rate * 0.50;

                // If deposit already exceeds 50%, 90-day becomes 0
                if ($this->deposit >= $halfTotal) {
                    $this->payment_90_day = 0;
                    $this->payment_45_day = max(0, $remainingAfterDeposit);
                } else {
                    $this->payment_90_day = $halfTotal - $this->deposit;
                    $this->payment_45_day = $this->safari_rate * 0.50;
                }
            }
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
