<?php

namespace App\Http\Controllers;

use App\Models\Traveler;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Traveler::with(['group.booking', 'flights', 'payment'])
            ->orderBy('last_name')
            ->orderBy('first_name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $travelers = $query->paginate(25);

        return view('clients.index', compact('travelers'));
    }

    public function show(Traveler $client)
    {
        $client->load(['group.booking', 'flights', 'payment', 'notes.creator']);

        return view('clients.show', compact('client'));
    }
}
