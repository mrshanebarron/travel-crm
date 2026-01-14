<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Flight extends Model
{
    protected $fillable = [
        'traveler_id',
        'type',
        'airport',
        'flight_number',
        'date',
        'time',
        'notes',
        'pickup_instructions',
        'dropoff_instructions',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function traveler(): BelongsTo
    {
        return $this->belongsTo(Traveler::class);
    }
}
