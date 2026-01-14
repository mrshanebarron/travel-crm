<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'name',
        'category',
        'country',
        'contact_name',
        'email',
        'phone',
        'whatsapp',
        'address',
        'bank_name',
        'bank_account',
        'swift_code',
        'payment_terms',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Vendor categories
     */
    public const CATEGORIES = [
        'Lodge' => 'Lodge / Camp / Hotel',
        'Transport' => 'Transport / Vehicle',
        'Guide' => 'Safari Guide',
        'Park' => 'Park Authority / Entry Fees',
        'Airline' => 'Airline / Internal Flights',
        'Restaurant' => 'Restaurant / Meals',
        'Activity' => 'Activity Provider',
        'Insurance' => 'Insurance',
        'Other' => 'Other',
    ];

    /**
     * Scope to get only active vendors
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
