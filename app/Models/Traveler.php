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

    public function addons(): HasMany
    {
        return $this->hasMany(TravelerAddon::class);
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

    /**
     * Calculate traveler's age at a specific date.
     */
    public function getAgeAtDate($date): ?int
    {
        if (!$this->dob) {
            return null;
        }

        $date = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);
        return $this->dob->diffInYears($date);
    }

    /**
     * Get age category for rate assignment.
     * Categories: adult (18+), child_12_17 (12-17), child_2_11 (2-11), infant (0-1)
     */
    public function getAgeCategoryAtDate($date): string
    {
        $age = $this->getAgeAtDate($date);

        if ($age === null) {
            return 'adult'; // Default to adult if no DOB
        }

        if ($age >= 18) {
            return 'adult';
        } elseif ($age >= 12) {
            return 'child_12_17';
        } elseif ($age >= 2) {
            return 'child_2_11';
        } else {
            return 'infant';
        }
    }

    /**
     * Get the age category for the booking's safari start date.
     */
    public function getAgeCategoryAttribute(): string
    {
        $booking = $this->group?->booking;
        $startDate = $booking?->start_date ?? now();
        return $this->getAgeCategoryAtDate($startDate);
    }
}
