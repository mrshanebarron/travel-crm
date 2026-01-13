<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'traveler_id',
        'safari_rate',
        'deposit',
        'payment_90_day',
        'payment_45_day',
    ];

    protected $casts = [
        'safari_rate' => 'decimal:2',
        'deposit' => 'decimal:2',
        'payment_90_day' => 'decimal:2',
        'payment_45_day' => 'decimal:2',
    ];

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
