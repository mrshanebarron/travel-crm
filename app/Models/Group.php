<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $fillable = [
        'booking_id',
        'group_number',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function travelers(): HasMany
    {
        return $this->hasMany(Traveler::class)->orderBy('order');
    }

    public function leadTraveler()
    {
        return $this->travelers()->where('is_lead', true)->first();
    }
}
