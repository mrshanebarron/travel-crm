<?php

namespace App\Exports;

use App\Models\LedgerEntry;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinancialReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
        return LedgerEntry::with(['booking.groups.travelers'])
            ->whereHas('booking', function ($q) {
                $q->whereBetween('start_date', [$this->startDate, $this->endDate]);
            })
            ->orderBy('date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Booking #',
            'Lead Traveler',
            'Type',
            'Category',
            'Vendor',
            'Description',
            'Amount',
        ];
    }

    public function map($entry): array
    {
        $leadTraveler = $entry->booking->groups->flatMap->travelers->firstWhere('is_lead', true);

        return [
            $entry->date->format('Y-m-d'),
            $entry->booking->booking_number,
            $leadTraveler ? "{$leadTraveler->last_name}, {$leadTraveler->first_name}" : '-',
            ucfirst($entry->type),
            $entry->category ?? '-',
            $entry->vendor_name ?? '-',
            $entry->description ?? '-',
            $entry->amount,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
