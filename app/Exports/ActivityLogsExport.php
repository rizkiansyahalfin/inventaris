<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ActivityLogsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $activityLogs;

    public function __construct($activityLogs)
    {
        $this->activityLogs = $activityLogs;
    }

    public function collection()
    {
        return $this->activityLogs;
    }

    public function headings(): array
    {
        return [
            'No',
            'Waktu',
            'Pengguna',
            'Modul',
            'Aksi',
            'Deskripsi',
            'IP Address',
            'User Agent',
        ];
    }

    public function map($log): array
    {
        static $no = 1;
        return [
            $no++,
            $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '',
            $log->user->name ?? 'Sistem',
            $log->module,
            $log->action,
            $log->description,
            $log->ip_address,
            $log->user_agent,
        ];
    }
} 