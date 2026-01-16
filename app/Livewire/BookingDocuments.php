<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\Document;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class BookingDocuments extends Component
{
    use WithFileUploads;

    public Booking $booking;
    public $name = '';
    public $category = 'lodge';
    public $file;

    protected $rules = [
        'name' => 'required|string|max:255',
        'category' => 'required|string',
        'file' => 'required|file|max:10240', // 10MB max
    ];

    public function mount(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function uploadDocument()
    {
        $this->validate();

        $path = $this->file->store('documents/' . $this->booking->id, 'public');

        $this->booking->documents()->create([
            'name' => $this->name,
            'category' => $this->category,
            'file_path' => $path,
            'file_name' => $this->file->getClientOriginalName(),
            'file_size' => $this->file->getSize(),
            'mime_type' => $this->file->getMimeType(),
        ]);

        $this->reset(['name', 'file']);
        $this->category = 'lodge';
        $this->booking->refresh();
    }

    public function deleteDocument(Document $document)
    {
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();
        $this->booking->refresh();
    }

    public function render()
    {
        return view('livewire.booking-documents', [
            'documents' => $this->booking->documents()->latest()->get(),
            'categoryLabels' => [
                'lodge' => 'Lodges/Camps',
                'arrival_departure_flight' => 'Arrival/Departure Flight',
                'internal_flight' => 'Internal Flights',
                'passport' => 'Passport',
                'safari_guide_invoice' => 'Safari Guide Invoices',
                'misc' => 'Miscellaneous',
            ],
        ]);
    }
}
