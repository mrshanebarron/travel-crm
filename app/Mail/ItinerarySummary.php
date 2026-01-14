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

class ItinerarySummary extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public Traveler $traveler
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Safari Itinerary - ' . $this->booking->destination_country,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.itinerary-summary',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
