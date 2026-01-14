<?php

namespace App\Exports;

use App\Models\Booking;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BookingsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected Carbon $startDate;
    protected Carbon $endDate;

    public function __construct(Carbon $startDate, Carbon $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return Booking::with(['groups.travelers', 'ledgerEntries', 'creator'])
            ->whereBetween('start_date', [$this->startDate, $this->endDate])
            ->orderBy('start_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Booking #',
            'Country',
            'Start Date',
            'End Date',
            'Status',
            'Lead Traveler',
            'Total Travelers',
            'Total Received',
            'Total Paid',
            'Net Profit',
            'Created By',
            'Created At',
        ];
    }

    public function map($booking): array
    {
        $leadTraveler = $booking->groups->flatMap->travelers->firstWhere('is_lead', true);
        $totalReceived = $booking->ledgerEntries->where('type', 'received')->sum('amount');
        $totalPaid = $booking->ledgerEntries->where('type', 'paid')->sum('amount');

        return [
            $booking->booking_number,
            $booking->country,
            $booking->start_date->format('Y-m-d'),
            $booking->end_date->format('Y-m-d'),
            ucfirst($booking->status),
            $leadTraveler ? "{$leadTraveler->last_name}, {$leadTraveler->first_name}" : '-',
            $booking->groups->sum(fn($g) => $g->travelers->count()),
            $totalReceived,
            $totalPaid,
            $totalReceived - $totalPaid,
            $booking->creator?->name ?? '-',
            $booking->created_at->format('Y-m-d H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
