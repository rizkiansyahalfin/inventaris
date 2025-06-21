<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StaffReportsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $staffReports;

    public function __construct($staffReports)
    {
        $this->staffReports = $staffReports;
    }

    public function collection()
    {
        return $this->staffReports;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Laporan',
            'Nama Petugas',
            'Jam Kerja',
            'Status',
            'Reviewer',
            'Tanggal Dibuat',
            'Aktivitas',
            'Tantangan',
        ];
    }

    public function map($report): array
    {
        static $no = 1;
        
        return [
            $no++,
            $report->report_date->format('d M Y'),
            $report->user->name,
            $report->hours_worked . ' jam',
            ucfirst($report->status),
            $report->reviewer ? $report->reviewer->name : '-',
            $report->created_at->format('d M Y H:i'),
            $report->activities,
            $report->challenges ?: '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB'],
                ],
            ],
        ];
    }
} 