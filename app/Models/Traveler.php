<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Traveler extends Model
{
    protected $fillable = [
        'group_id',
        'first_name',
        'last_name',
        'dob',
        'email',
        'phone',
        'is_lead',
        'order',
    ];

    protected $casts = [
        'dob' => 'date',
        'is_lead' => 'boolean',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function booking()
    {
        return $this->group->booking;
    }

    public function flights(): HasMany
    {
        return $this->hasMany(Flight::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ClientNote::class);
    }

    public function arrivalFlight()
    {
        return $this->flights()->where('type', 'arrival')->first();
    }

    public function departureFlight()
    {
        return $this->flights()->where('type', 'departure')->first();
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
