<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\LedgerEntry;
use App\Models\Traveler;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReconciliationController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->start_date) : now()->subMonths(3);
        $endDate = $request->get('end_date') ? Carbon::parse($request->end_date) : now()->addMonths(6);

        // Get bookings with payment info
        $bookings = Booking::with([
            'groups.travelers.payment',
            'ledgerEntries',
        ])
            ->whereBetween('start_date', [$startDate, $endDate])
            ->orderBy('start_date')
            ->get();

        // Calculate reconciliation data for each booking
        $reconciliationData = $bookings->map(function ($booking) {
            $travelers = $booking->travelers;
            $ledgerReceived = $booking->ledgerEntries->where('type', 'received')->sum('amount');
            $ledgerPaid = $booking->ledgerEntries->where('type', 'paid')->sum('amount');

            // Expected from travelers
            $totalExpected = $travelers->sum(fn($t) => $t->payment?->safari_rate ?? 0);

            // Payment breakdown
            $depositExpected = $totalExpected * 0.25;
            $secondPaymentExpected = $totalExpected * 0.25;
            $finalPaymentExpected = $totalExpected * 0.50;

            // Days until trip
            $daysUntilTrip = now()->diffInDays($booking->start_date, false);

            // Payment due status
            $depositDue = true; // Always due once booked
            $secondPaymentDue = $daysUntilTrip <= 90;
            $finalPaymentDue = $daysUntilTrip <= 45;

            // Calculate which payments should have been received by now
            $expectedToDate = $depositExpected;
            if ($secondPaymentDue) $expectedToDate += $secondPaymentExpected;
            if ($finalPaymentDue) $expectedToDate += $finalPaymentExpected;

            return [
                'booking' => $booking,
                'lead_traveler' => $booking->leadTraveler(),
                'traveler_count' => $travelers->count(),
                'total_expected' => $totalExpected,
                'expected_to_date' => $expectedToDate,
                'total_received' => $ledgerReceived,
                'total_paid' => $ledgerPaid,
                'variance' => $ledgerReceived - $expectedToDate,
                'profit' => $ledgerReceived - $ledgerPaid,
                'days_until_trip' => $daysUntilTrip,
                'deposit_due' => $depositDue,
                'second_payment_due' => $secondPaymentDue,
                'final_payment_due' => $finalPaymentDue,
                'status' => $this->getPaymentStatus($ledgerReceived, $expectedToDate, $totalExpected),
            ];
        });

        // Summary stats
        $summary = [
            'total_expected' => $reconciliationData->sum('total_expected'),
            'total_expected_to_date' => $reconciliationData->sum('expected_to_date'),
            'total_received' => $reconciliationData->sum('total_received'),
            'total_paid' => $reconciliationData->sum('total_paid'),
            'total_variance' => $reconciliationData->sum('variance'),
            'total_profit' => $reconciliationData->sum('profit'),
            'overdue_count' => $reconciliationData->where('status', 'overdue')->count(),
            'pending_count' => $reconciliationData->where('status', 'pending')->count(),
            'current_count' => $reconciliationData->where('status', 'current')->count(),
        ];

        return view('reconciliation.index', compact(
            'reconciliationData',
            'summary',
            'startDate',
            'endDate'
        ));
    }

    protected function getPaymentStatus($received, $expectedToDate, $totalExpected): string
    {
        if ($received >= $totalExpected) {
            return 'paid';
        } elseif ($received >= $expectedToDate) {
            return 'current';
        } elseif ($received > 0 && $received < $expectedToDate) {
            return 'pending';
        } else {
            return 'overdue';
        }
    }
}
