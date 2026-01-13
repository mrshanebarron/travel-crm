<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // 10MB max
            'category' => 'required|in:flight,lodge,passport,misc',
        ]);

        $path = $request->file('file')->store('documents/' . $booking->id, 'public');

        $booking->documents()->create([
            'name' => $validated['name'],
            'file_path' => $path,
            'category' => $validated['category'],
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Document uploaded successfully.');
    }

    public function download(Document $document)
    {
        return Storage::disk('public')->download($document->file_path, $document->name);
    }

    public function destroy(Document $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->back()->with('success', 'Document deleted successfully.');
    }
}
