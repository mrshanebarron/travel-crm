<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Traveler;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return view('search.index', [
                'query' => $query,
                'bookings' => collect(),
                'travelers' => collect(),
            ]);
        }

        // Search bookings by booking number or country
        $bookings = Booking::with(['groups.travelers'])
            ->where(function ($q) use ($query) {
                $q->where('booking_number', 'like', "%{$query}%")
                  ->orWhere('country', 'like', "%{$query}%");
            })
            ->orderBy('start_date', 'desc')
            ->limit(10)
            ->get();

        // Search travelers by name, email, or phone
        // Only include travelers whose group has a valid booking
        $travelers = Traveler::with(['group.booking'])
            ->whereHas('group.booking')
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();

        return view('search.index', compact('query', 'bookings', 'travelers'));
    }
}
