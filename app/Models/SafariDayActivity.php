<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SafariDayActivity extends Model
{
    protected $fillable = [
        'safari_day_id',
        'period',
        'activity',
        'sort_order',
    ];

    public function safariDay(): BelongsTo
    {
        return $this->belongsTo(SafariDay::class);
    }
}
