<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SafariDay extends Model
{
    protected $fillable = [
        'booking_id',
        'day_number',
        'date',
        'location',
        'lodge',
        'morning_activity',
        'midday_activity',
        'afternoon_activity',
        'other_activities',
        'meal_plan',
        'drink_plan',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
