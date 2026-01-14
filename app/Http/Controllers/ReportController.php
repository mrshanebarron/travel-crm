<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Traveler;
use App\Models\LedgerEntry;
use App\Models\Transfer;
use App\Exports\BookingsExport;
use App\Exports\FinancialReportExport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->start_date) : now()->startOfYear();
        $endDate = $request->get('end_date') ? Carbon::parse($request->end_date) : now()->endOfYear();

        // Booking Statistics
        $bookingStats = [
            'total' => Booking::whereBetween('start_date', [$startDate, $endDate])->count(),
            'upcoming' => Booking::where('start_date', '>', now())->count(),
            'active' => Booking::where('start_date', '<=', now())->where('end_date', '>=', now())->count(),
            'completed' => Booking::where('end_date', '<', now())->whereBetween('start_date', [$startDate, $endDate])->count(),
        ];

        // Traveler Statistics
        $travelerStats = [
            'total' => Traveler::whereHas('group.booking', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate]);
            })->count(),
            'adults' => Traveler::whereHas('group.booking', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate]);
            })->whereNotNull('dob')->whereRaw('TIMESTAMPDIFF(YEAR, dob, CURDATE()) >= 18')->count(),
        ];

        // Financial Summary
        $financialStats = [
            'total_received' => LedgerEntry::whereHas('booking', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate]);
            })->where('type', 'received')->sum('amount'),
            'total_paid' => LedgerEntry::whereHas('booking', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate]);
            })->where('type', 'paid')->sum('amount'),
        ];
        $financialStats['profit'] = $financialStats['total_received'] - $financialStats['total_paid'];

        // Recent Bookings by Month
        $monthlyBookings = Booking::selectRaw('MONTH(start_date) as month, COUNT(*) as count')
            ->whereBetween('start_date', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Top Countries
        $topCountries = Booking::selectRaw('country, COUNT(*) as count')
            ->whereBetween('start_date', [$startDate, $endDate])
            ->groupBy('country')
            ->orderByDesc('count')
            ->limit(5)
            ->pluck('count', 'country')
            ->toArray();

        // Recent Transfers
        $transfers = Transfer::with('creator')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'bookingStats',
            'travelerStats',
            'financialStats',
            'monthlyBookings',
            'topCountries',
            'transfers'
        ));
    }

    /**
     * Export bookings report to Excel.
     */
    public function exportBookings(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->start_date) : now()->startOfYear();
        $endDate = $request->get('end_date') ? Carbon::parse($request->end_date) : now()->endOfYear();

        $filename = 'bookings-report-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.xlsx';

        return Excel::download(new BookingsExport($startDate, $endDate), $filename);
    }

    /**
     * Export financial report to Excel.
     */
    public function exportFinancial(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->start_date) : now()->startOfYear();
        $endDate = $request->get('end_date') ? Carbon::parse($request->end_date) : now()->endOfYear();

        $filename = 'financial-report-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.xlsx';

        return Excel::download(new FinancialReportExport($startDate, $endDate), $filename);
    }
}
