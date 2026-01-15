<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelerAddon extends Model
{
    protected $fillable = [
        'traveler_id',
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

    public function traveler(): BelongsTo
    {
        return $this->belongsTo(Traveler::class);
    }

    public function ledgerEntry(): BelongsTo
    {
        return $this->belongsTo(LedgerEntry::class);
    }
}
