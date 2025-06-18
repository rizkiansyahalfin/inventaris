<?php

namespace Database\Seeders;

use App\Models\SystemConfig;
use Illuminate\Database\Seeder;

class SystemConfigSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            [
                'key' => 'site_name',
                'value' => 'Sistem Inventaris Pondok Pesantren',
                'description' => 'Nama aplikasi inventaris pondok',
            ],
            [
                'key' => 'site_description',
                'value' => 'Sistem Manajemen Inventaris Barang Pondok Pesantren',
                'description' => 'Deskripsi aplikasi inventaris',
            ],
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'description' => 'Mode maintenance aplikasi',
            ],
            [
                'key' => 'min_stock_notification',
                'value' => '5',
                'description' => 'Jumlah minimum stok untuk notifikasi',
            ],
            [
                'key' => 'max_borrow_days',
                'value' => '14',
                'description' => 'Maksimal hari peminjaman barang',
            ],
            [
                'key' => 'max_borrow_items',
                'value' => '3',
                'description' => 'Maksimal item yang dapat dipinjam per santri',
            ],
            [
                'key' => 'late_fine_per_day',
                'value' => '5000',
                'description' => 'Denda keterlambatan per hari (Rp)',
            ],
            [
                'key' => 'office_hours_start',
                'value' => '08:00',
                'description' => 'Jam buka kantor inventaris',
            ],
            [
                'key' => 'office_hours_end',
                'value' => '16:00',
                'description' => 'Jam tutup kantor inventaris',
            ],
            [
                'key' => 'auto_approve_borrow',
                'value' => 'false',
                'description' => 'Otomatis menyetujui peminjaman',
            ],
            [
                'key' => 'require_approval_for_electronics',
                'value' => 'true',
                'description' => 'Memerlukan persetujuan untuk peminjaman elektronik',
            ],
            [
                'key' => 'require_approval_for_expensive_items',
                'value' => 'true',
                'description' => 'Memerlukan persetujuan untuk peminjaman barang mahal',
            ],
            [
                'key' => 'expensive_item_threshold',
                'value' => '1000000',
                'description' => 'Batas harga barang mahal (Rp)',
            ],
            [
                'key' => 'notification_email',
                'value' => 'inventaris@pondok.com',
                'description' => 'Email untuk notifikasi sistem',
            ],
            [
                'key' => 'backup_frequency',
                'value' => 'daily',
                'description' => 'Frekuensi backup database',
            ],
            [
                'key' => 'qr_code_enabled',
                'value' => 'true',
                'description' => 'Aktifkan QR Code untuk barang',
            ],
            [
                'key' => 'barcode_enabled',
                'value' => 'true',
                'description' => 'Aktifkan Barcode untuk barang',
            ],
            [
                'key' => 'report_auto_generate',
                'value' => 'true',
                'description' => 'Otomatis generate laporan bulanan',
            ],
            [
                'key' => 'stock_opname_frequency',
                'value' => 'monthly',
                'description' => 'Frekuensi stock opname',
            ],
            [
                'key' => 'maintenance_reminder_days',
                'value' => '30',
                'description' => 'Jumlah hari sebelum pengingat maintenance',
            ],
        ];

        foreach ($configs as $config) {
            SystemConfig::firstOrCreate(
                ['key' => $config['key']],
                $config
            );
        }
    }
} 