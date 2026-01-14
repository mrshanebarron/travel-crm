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

class PaymentReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public Traveler $traveler,
        public string $paymentType, // 'deposit', 'second', 'final'
        public float $amountDue,
        public string $dueDate
    ) {}

    public function envelope(): Envelope
    {
        $typeLabels = [
            'deposit' => '25% Deposit',
            'second' => 'Second Payment (25%)',
            'final' => 'Final Payment (50%)',
        ];

        return new Envelope(
            subject: 'Payment Reminder: ' . ($typeLabels[$this->paymentType] ?? 'Payment') . ' Due',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment-reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
