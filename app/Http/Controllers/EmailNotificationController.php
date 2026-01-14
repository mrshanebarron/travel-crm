<?php

namespace App\Http\Controllers;

use App\Mail\BookingConfirmation;
use App\Mail\DocumentRequest;
use App\Mail\ItinerarySummary;
use App\Mail\PaymentReminder;
use App\Models\Booking;
use App\Models\EmailLog;
use App\Models\Traveler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailNotificationController extends Controller
{
    public function sendConfirmation(Request $request, Booking $booking, Traveler $traveler)
    {
        $this->authorize('update', $booking);

        if (!$traveler->email) {
            return back()->with('error', 'Traveler does not have an email address.');
        }

        $mail = new BookingConfirmation($booking, $traveler);
        Mail::to($traveler->email, $traveler->full_name)->send($mail);

        EmailLog::create([
            'booking_id' => $booking->id,
            'traveler_id' => $traveler->id,
            'sent_by' => auth()->id(),
            'email_type' => 'confirmation',
            'recipient_email' => $traveler->email,
            'recipient_name' => $traveler->full_name,
            'subject' => $mail->envelope()->subject,
        ]);

        return back()->with('success', 'Booking confirmation sent to ' . $traveler->email);
    }

    public function sendPaymentReminder(Request $request, Booking $booking, Traveler $traveler)
    {
        $this->authorize('update', $booking);

        $request->validate([
            'payment_type' => 'required|in:deposit,second,final',
            'amount_due' => 'required|numeric|min:0',
            'due_date' => 'required|date',
        ]);

        if (!$traveler->email) {
            return back()->with('error', 'Traveler does not have an email address.');
        }

        $mail = new PaymentReminder(
            $booking,
            $traveler,
            $request->payment_type,
            $request->amount_due,
            $request->due_date
        );
        Mail::to($traveler->email, $traveler->full_name)->send($mail);

        EmailLog::create([
            'booking_id' => $booking->id,
            'traveler_id' => $traveler->id,
            'sent_by' => auth()->id(),
            'email_type' => 'payment_reminder',
            'recipient_email' => $traveler->email,
            'recipient_name' => $traveler->full_name,
            'subject' => $mail->envelope()->subject,
            'notes' => "Payment type: {$request->payment_type}, Amount: \${$request->amount_due}, Due: {$request->due_date}",
        ]);

        return back()->with('success', 'Payment reminder sent to ' . $traveler->email);
    }

    public function sendDocumentRequest(Request $request, Booking $booking, Traveler $traveler)
    {
        $this->authorize('update', $booking);

        if (!$traveler->email) {
            return back()->with('error', 'Traveler does not have an email address.');
        }

        $documentsNeeded = $request->input('documents', []);
        if (is_string($documentsNeeded)) {
            $documentsNeeded = array_filter(array_map('trim', explode("\n", $documentsNeeded)));
        }

        $mail = new DocumentRequest($booking, $traveler, $documentsNeeded);
        Mail::to($traveler->email, $traveler->full_name)->send($mail);

        EmailLog::create([
            'booking_id' => $booking->id,
            'traveler_id' => $traveler->id,
            'sent_by' => auth()->id(),
            'email_type' => 'document_request',
            'recipient_email' => $traveler->email,
            'recipient_name' => $traveler->full_name,
            'subject' => $mail->envelope()->subject,
            'notes' => count($documentsNeeded) > 0 ? implode(', ', $documentsNeeded) : 'Standard documents',
        ]);

        return back()->with('success', 'Document request sent to ' . $traveler->email);
    }

    public function sendItinerary(Request $request, Booking $booking, Traveler $traveler)
    {
        $this->authorize('update', $booking);

        if (!$traveler->email) {
            return back()->with('error', 'Traveler does not have an email address.');
        }

        $booking->load('safariDays');

        $mail = new ItinerarySummary($booking, $traveler);
        Mail::to($traveler->email, $traveler->full_name)->send($mail);

        EmailLog::create([
            'booking_id' => $booking->id,
            'traveler_id' => $traveler->id,
            'sent_by' => auth()->id(),
            'email_type' => 'itinerary',
            'recipient_email' => $traveler->email,
            'recipient_name' => $traveler->full_name,
            'subject' => $mail->envelope()->subject,
        ]);

        return back()->with('success', 'Itinerary sent to ' . $traveler->email);
    }

    public function sendBulkEmails(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $request->validate([
            'email_type' => 'required|in:confirmation,document_request,itinerary',
            'traveler_ids' => 'required|array',
            'traveler_ids.*' => 'exists:travelers,id',
        ]);

        $travelers = Traveler::whereIn('id', $request->traveler_ids)
            ->whereNotNull('email')
            ->get();

        if ($travelers->isEmpty()) {
            return back()->with('error', 'No travelers with email addresses selected.');
        }

        $sent = 0;
        foreach ($travelers as $traveler) {
            switch ($request->email_type) {
                case 'confirmation':
                    $mail = new BookingConfirmation($booking, $traveler);
                    break;
                case 'document_request':
                    $mail = new DocumentRequest($booking, $traveler);
                    break;
                case 'itinerary':
                    $booking->load('safariDays');
                    $mail = new ItinerarySummary($booking, $traveler);
                    break;
                default:
                    continue 2;
            }

            Mail::to($traveler->email, $traveler->full_name)->send($mail);

            EmailLog::create([
                'booking_id' => $booking->id,
                'traveler_id' => $traveler->id,
                'sent_by' => auth()->id(),
                'email_type' => $request->email_type,
                'recipient_email' => $traveler->email,
                'recipient_name' => $traveler->full_name,
                'subject' => $mail->envelope()->subject,
                'notes' => 'Bulk send',
            ]);

            $sent++;
        }

        return back()->with('success', "Emails sent to {$sent} travelers.");
    }
}
