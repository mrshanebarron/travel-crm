<?php

namespace App\Http\Controllers;

use App\Models\ClientNote;
use App\Models\Traveler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ClientNoteController extends Controller
{
    public function store(Request $request, Traveler $traveler)
    {
        Gate::authorize('update', $traveler->group->booking);

        $validated = $request->validate([
            'type' => 'required|in:note,call,email,meeting',
            'content' => 'required|string',
            'contacted_at' => 'nullable|date',
        ]);

        $traveler->notes()->create([
            'type' => $validated['type'],
            'content' => $validated['content'],
            'contacted_at' => $validated['contacted_at'] ?? now(),
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Note added successfully.');
    }

    public function destroy(ClientNote $clientNote)
    {
        Gate::authorize('update', $clientNote->traveler->group->booking);

        $clientNote->delete();

        return back()->with('success', 'Note deleted.');
    }
}
