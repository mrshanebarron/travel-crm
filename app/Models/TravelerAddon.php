<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelerAddon extends Model
{
    protected $fillable = [
        'traveler_id',
        'type',
        'experience_name',
        'cost_per_person',
        'notes',
        'paid',
        'ledger_entry_id',
    ];

    protected $casts = [
        'cost_per_person' => 'decimal:2',
        'paid' => 'boolean',
    ];

    /**
     * Check if this is a credit (negative adjustment)
     */
    public function isCredit(): bool
    {
        return $this->type === 'credit';
    }

    /**
     * Get the effective amount (negative for credits)
     */
    public function getEffectiveAmountAttribute(): float
    {
        return $this->isCredit() ? -abs($this->cost_per_person) : $this->cost_per_person;
    }

    public function traveler(): BelongsTo
    {
        return $this->belongsTo(Traveler::class);
    }

    public function ledgerEntry(): BelongsTo
    {
        return $this->belongsTo(LedgerEntry::class);
    }
}
