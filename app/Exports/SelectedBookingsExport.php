<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SelectedBookingsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected array $bookingIds;

    public function __construct(array $bookingIds)
    {
        $this->bookingIds = $bookingIds;
    }

    public function collection()
    {
        return Booking::with(['groups.travelers.payment', 'ledgerEntries'])
            ->whereIn('id', $this->bookingIds)
            ->orderBy('start_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Booking #',
            'Destination',
            'Start Date',
            'End Date',
            'Status',
            'Lead Traveler',
            'Total Travelers',
            'Total Expected',
            'Total Received',
            'Balance Due',
            'Total Paid to Vendors',
            'Profit/Loss',
        ];
    }

    public function map($booking): array
    {
        $travelers = $booking->travelers;
        $lead = $travelers->where('is_lead', true)->first();

        $totalExpected = $travelers->sum(fn($t) => $t->payment?->safari_rate ?? 0);
        $totalReceived = $booking->ledgerEntries->where('type', 'received')->sum('amount');
        $totalPaid = $booking->ledgerEntries->where('type', 'paid')->sum('amount');

        return [
            $booking->booking_number,
            $booking->country,
            $booking->start_date->format('M d, Y'),
            $booking->end_date->format('M d, Y'),
            ucfirst($booking->status),
            $lead ? $lead->last_name . ', ' . $lead->first_name : '-',
            $travelers->count(),
            '$' . number_format($totalExpected, 2),
            '$' . number_format($totalReceived, 2),
            '$' . number_format($totalExpected - $totalReceived, 2),
            '$' . number_format($totalPaid, 2),
            '$' . number_format($totalReceived - $totalPaid, 2),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
