<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ItemsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function collection()
    {
        return $this->items;
    }

    public function headings(): array
    {
        return [
            'Kode',
            'Nama',
            'Kategori',
            'Status',
            'Kondisi',
            'Lokasi',
            'Harga Beli',
            'Tanggal Pembelian'
        ];
    }

    public function map($item): array
    {
        return [
            $item->code,
            $item->name,
            $item->categories->pluck('name')->join(', '),
            $item->status,
            $item->condition,
            $item->location,
            number_format($item->purchase_price, 2),
            $item->purchase_date->format('d/m/Y')
        ];
    }
} 