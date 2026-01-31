<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guide extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
        'date_from',
        'date_to',
        'notes',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
    ];

    public static function getKenyaGuides()
    {
        return [
            'Sammy',
            'George',
            'Vincent',
            'Joseph',
            'Kim',
            'Wanderi',
            'Boniface',
        ];
    }

    public static function getCountries()
    {
        return [
            'Kenya',
            'Tanzania',
            'Uganda',
            'Rwanda',
        ];
    }
}
