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
        'deposit_paid',
        'payment_90_day',
        'payment_90_day_paid',
        'payment_45_day',
        'payment_45_day_paid',
    ];

    protected $casts = [
        'safari_rate' => 'decimal:2',
        'original_rate' => 'decimal:2',
        'deposit' => 'decimal:2',
        'deposit_locked' => 'boolean',
        'deposit_paid' => 'boolean',
        'payment_90_day' => 'decimal:2',
        'payment_90_day_paid' => 'boolean',
        'payment_45_day' => 'decimal:2',
        'payment_45_day_paid' => 'boolean',
    ];

    /**
     * Recalculate payment schedule when rate changes.
     *
     * Key rules:
     * - Deposit is locked once set (amount fixed, but can be recalculated if rate changes before first payment)
     * - If 90-day payment not yet paid, it adjusts so deposit + 90-day = 50%
     * - 45-day payment is always the remainder (50% for normal, or whatever's left)
     * - Paid payments are NEVER changed
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
            // Rate changed - recalculate unpaid amounts only
            // Paid amounts stay fixed, unpaid amounts absorb the difference

            $paidAmount = 0;
            if ($this->deposit_paid) {
                $paidAmount += $this->deposit;
            }
            if ($this->payment_90_day_paid) {
                $paidAmount += $this->payment_90_day;
            }
            if ($this->payment_45_day_paid) {
                $paidAmount += $this->payment_45_day;
            }

            $remainingToPay = $this->safari_rate - $paidAmount;

            // Recalculate unpaid amounts
            if (!$this->deposit_paid) {
                // Deposit not yet paid - this shouldn't normally happen after locking
                // but handle it anyway
                $this->deposit = $this->safari_rate * 0.25;
                $remainingToPay = $this->safari_rate - $this->deposit;

                if (!$this->payment_90_day_paid && $this->payment_90_day > 0) {
                    // 90-day not paid - deposit + 90-day should = 50%
                    $halfTotal = $this->safari_rate * 0.50;
                    $this->payment_90_day = $halfTotal - $this->deposit;
                    $this->payment_45_day = $this->safari_rate * 0.50;
                } else {
                    // 90-day already paid or was 0 - remainder goes to 45-day
                    $this->payment_45_day = max(0, $remainingToPay - ($this->payment_90_day_paid ? $this->payment_90_day : 0));
                }
            } elseif (!$this->payment_90_day_paid && $this->payment_90_day > 0) {
                // Deposit paid, 90-day not paid
                // Recalculate so deposit + 90-day = 50%, 45-day = 50%
                $halfTotal = $this->safari_rate * 0.50;

                if ($this->deposit >= $halfTotal) {
                    // Deposit already covers 50% or more - 90-day becomes 0
                    $this->payment_90_day = 0;
                    $this->payment_45_day = max(0, $this->safari_rate - $this->deposit);
                } else {
                    $this->payment_90_day = $halfTotal - $this->deposit;
                    $this->payment_45_day = $this->safari_rate * 0.50;
                }
            } elseif (!$this->payment_45_day_paid) {
                // Only 45-day is unpaid - it absorbs all remaining
                $this->payment_45_day = max(0, $remainingToPay);
            }
            // If all paid, nothing changes
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
