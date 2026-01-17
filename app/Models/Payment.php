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
     * - Unpaid payments ALWAYS recalculate based on new rate (25%/25%/50%)
     * - Paid payments stay fixed (they represent actual money received)
     * - Unpaid payments absorb the difference from rate changes
     *
     * Booking Timing Logic:
     * - Normal booking (> 90 days out): 25% deposit, 25% at 90-day, 50% at 45-day
     * - Within 90 days: 50% deposit (skip 90-day payment), 50% at 45-day
     * - Within 45 days: 100% due immediately
     */
    public function recalculateSchedule(?int $daysUntilSafari = null): void
    {
        // Calculate what's already been paid (don't change these amounts)
        $paidDeposit = $this->deposit_paid ? $this->deposit : 0;
        $paid90Day = $this->payment_90_day_paid ? $this->payment_90_day : 0;
        $paid45Day = $this->payment_45_day_paid ? $this->payment_45_day : 0;
        $totalPaid = $paidDeposit + $paid90Day + $paid45Day;

        // Determine what the schedule SHOULD be based on current rate
        if ($daysUntilSafari !== null && $daysUntilSafari <= 45) {
            // Within 45 days - 100% due immediately
            $targetDeposit = $this->safari_rate;
            $target90Day = 0;
            $target45Day = 0;
        } elseif ($daysUntilSafari !== null && $daysUntilSafari <= 90) {
            // Within 90 days - 50% deposit, skip 90-day, 50% at 45-day
            $targetDeposit = $this->safari_rate * 0.50;
            $target90Day = 0;
            $target45Day = $this->safari_rate * 0.50;
        } else {
            // Standard booking - 25/25/50 split
            $targetDeposit = $this->safari_rate * 0.25;
            $target90Day = $this->safari_rate * 0.25;
            $target45Day = $this->safari_rate * 0.50;
        }

        // Update unpaid amounts to target values
        // Paid amounts stay locked to what was actually received
        if (!$this->deposit_paid) {
            $this->deposit = $targetDeposit;
        }

        if (!$this->payment_90_day_paid) {
            if ($this->deposit_paid) {
                // Deposit already paid - adjust 90-day so deposit + 90-day = 50%
                $halfTotal = $this->safari_rate * 0.50;
                $this->payment_90_day = max(0, $halfTotal - $this->deposit);
            } else {
                $this->payment_90_day = $target90Day;
            }
        }

        if (!$this->payment_45_day_paid) {
            // 45-day absorbs the remainder
            $this->payment_45_day = max(0, $this->safari_rate - $this->deposit - $this->payment_90_day);
        }

        // Track original rate and mark as having a schedule set
        if (!$this->deposit_locked) {
            $this->original_rate = $this->safari_rate;
            $this->deposit_locked = true;
        }
    }

    public function traveler(): BelongsTo
    {
        return $this->belongsTo(Traveler::class);
    }

    /**
     * Get total rate including add-ons and credits.
     * This is the amount that should be used for payment schedule calculations.
     */
    public function getTotalRateWithAddonsAttribute(): float
    {
        $traveler = $this->traveler;
        if (!$traveler) {
            return (float) $this->safari_rate;
        }

        $addons = $traveler->addons->where('type', '!=', 'credit')->sum('cost_per_person');
        $credits = $traveler->addons->where('type', 'credit')->sum('cost_per_person');

        return (float) $this->safari_rate + $addons - $credits;
    }

    /**
     * Recalculate payment schedule based on total rate (including add-ons).
     * Called when add-ons change or dates change.
     */
    public function recalculateWithAddons(?int $daysUntilSafari = null): void
    {
        $totalRate = $this->total_rate_with_addons;

        // Calculate what's already been paid (don't touch paid amounts)
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

        $remainingToPay = $totalRate - $paidAmount;

        // If all payments are made, nothing to recalculate
        if ($this->deposit_paid && $this->payment_90_day_paid && $this->payment_45_day_paid) {
            return;
        }

        // Determine schedule based on days until safari
        if ($daysUntilSafari !== null && $daysUntilSafari <= 0) {
            // Already on safari - any remaining is 100% due now
            if (!$this->deposit_paid) {
                $this->deposit = $remainingToPay;
                $this->payment_90_day = 0;
                $this->payment_45_day = 0;
            } elseif (!$this->payment_90_day_paid) {
                $this->payment_90_day = $remainingToPay;
                $this->payment_45_day = 0;
            } elseif (!$this->payment_45_day_paid) {
                $this->payment_45_day = $remainingToPay;
            }
        } elseif ($daysUntilSafari !== null && $daysUntilSafari <= 45) {
            // Within 45 days - remaining split between available payments
            if (!$this->deposit_paid) {
                $this->deposit = $remainingToPay;
                $this->payment_90_day = 0;
                $this->payment_45_day = 0;
            } elseif (!$this->payment_45_day_paid) {
                $this->payment_45_day = $remainingToPay;
                if (!$this->payment_90_day_paid) {
                    $this->payment_90_day = 0;
                }
            }
        } elseif ($daysUntilSafari !== null && $daysUntilSafari <= 90) {
            // Within 90 days - 50/50 split of remaining
            if (!$this->deposit_paid) {
                $this->deposit = $remainingToPay * 0.50;
                $this->payment_90_day = 0;
                $this->payment_45_day = $remainingToPay * 0.50;
            } else {
                // Deposit paid - all remaining goes to 45-day
                $this->payment_90_day = 0;
                if (!$this->payment_45_day_paid) {
                    $this->payment_45_day = $remainingToPay;
                }
            }
        } else {
            // Normal booking (> 90 days) - maintain 25/25/50 ratio on remaining
            // But respect what's already been paid
            if (!$this->deposit_paid) {
                // Target: deposit + 90-day = 50%, 45-day = 50%
                $targetFirstHalf = $totalRate * 0.50;
                $this->deposit = $totalRate * 0.25;
                $this->payment_90_day = $totalRate * 0.25;
                $this->payment_45_day = $totalRate * 0.50;
            } elseif (!$this->payment_90_day_paid) {
                // Deposit paid - recalc 90-day so deposit + 90-day = 50%
                $targetFirstHalf = $totalRate * 0.50;
                $this->payment_90_day = max(0, $targetFirstHalf - $this->deposit);
                if (!$this->payment_45_day_paid) {
                    $this->payment_45_day = max(0, $totalRate - $this->deposit - $this->payment_90_day);
                }
            } elseif (!$this->payment_45_day_paid) {
                // Deposit and 90-day paid - 45-day absorbs remainder
                $this->payment_45_day = max(0, $remainingToPay);
            }
        }
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->deposit + $this->payment_90_day + $this->payment_45_day;
    }

    public function getBalanceAttribute(): float
    {
        return $this->total_rate_with_addons - $this->total_paid;
    }

    public function getIsFullyPaidAttribute(): bool
    {
        return $this->balance <= 0;
    }
}
