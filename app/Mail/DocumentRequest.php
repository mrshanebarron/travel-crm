<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Traveler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentRequest extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public Traveler $traveler,
        public array $documentsNeeded = []
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Documents Needed for Your Safari Trip',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.document-request',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
