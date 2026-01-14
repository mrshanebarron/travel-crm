<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    protected $fillable = [
        'booking_id',
        'group_id',
        'type',
        'custom_type',
        'adults',
        'children_12_17',
        'children_2_11',
        'children_under_2',
    ];

    protected $casts = [
        'adults' => 'integer',
        'children_12_17' => 'integer',
        'children_2_11' => 'integer',
        'children_under_2' => 'integer',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function getTotalOccupantsAttribute(): int
    {
        return $this->adults + $this->children_12_17 + $this->children_2_11 + $this->children_under_2;
    }

    public function getDisplayTypeAttribute(): string
    {
        return $this->type === 'other' ? $this->custom_type : ucfirst($this->type);
    }
}
