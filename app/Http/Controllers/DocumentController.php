<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Document;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // 10MB max
            'category' => 'required|in:lodge,arrival_departure_flight,internal_flight,passport,safari_guide_invoice,misc',
        ]);

        $path = $request->file('file')->store('documents/' . $booking->id, 'public');

        $document = $booking->documents()->create([
            'name' => $validated['name'],
            'file_path' => $path,
            'category' => $validated['category'],
            'uploaded_by' => auth()->id(),
        ]);

        ActivityLog::logAction(
            $booking->id,
            'document_uploaded',
            "Uploaded document: {$validated['name']} ({$validated['category']})",
            'Document',
            $document->id
        );

        return redirect()->back()->with('success', 'Document uploaded successfully.');
    }

    public function download(Document $document)
    {
        Gate::authorize('view', $document->booking);

        return Storage::disk('public')->download($document->file_path, $document->name);
    }

    public function destroy(Document $document)
    {
        Gate::authorize('update', $document->booking);

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->back()->with('success', 'Document deleted successfully.');
    }
}
