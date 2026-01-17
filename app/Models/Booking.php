<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_number',
        'country',
        'start_date',
        'end_date',
        'status',
        'guides',
        'created_by',
        'safari_office_url',
        'intake_token',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'guides' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function safariDays(): HasMany
    {
        return $this->hasMany(SafariDay::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class);
    }

    public function transferExpenses(): HasMany
    {
        return $this->hasMany(TransferExpense::class);
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class);
    }

    // Get all travelers through groups
    public function travelers()
    {
        return $this->hasManyThrough(Traveler::class, Group::class);
    }

    // Get lead traveler (first traveler in Group 1)
    public function leadTraveler()
    {
        return $this->travelers()->where('is_lead', true)->first();
    }

    // Generate next booking number
    public static function generateBookingNumber(): string
    {
        $year = date('Y');
        $prefix = 'JA-' . $year . '-';
        $lastBooking = self::where('booking_number', 'like', $prefix . '%')
            ->orderBy('booking_number', 'desc')
            ->first();

        if ($lastBooking) {
            $lastNumber = (int) substr($lastBooking->booking_number, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get display name for dropdowns: "Last Name, First Name (Start Date)"
     */
    public function getDisplayNameAttribute(): string
    {
        $lead = $this->leadTraveler();
        $travelerName = $lead
            ? "{$lead->last_name}, {$lead->first_name}"
            : $this->booking_number;

        return "{$travelerName} ({$this->start_date->format('M j, Y')})";
    }
}
