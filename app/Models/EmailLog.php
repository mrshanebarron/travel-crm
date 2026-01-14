<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    protected $fillable = [
        'booking_id',
        'traveler_id',
        'sent_by',
        'email_type',
        'recipient_email',
        'recipient_name',
        'subject',
        'notes',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public const TYPES = [
        'confirmation' => 'Booking Confirmation',
        'payment_reminder' => 'Payment Reminder',
        'document_request' => 'Document Request',
        'itinerary' => 'Itinerary Summary',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function traveler(): BelongsTo
    {
        return $this->belongsTo(Traveler::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function getTypeLabel(): string
    {
        return self::TYPES[$this->email_type] ?? $this->email_type;
    }
}
