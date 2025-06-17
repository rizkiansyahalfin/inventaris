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
                'value' => 'Sistem Inventaris',
                'description' => 'Nama aplikasi',
            ],
            [
                'key' => 'site_description',
                'value' => 'Sistem Manajemen Inventaris Barang',
                'description' => 'Deskripsi aplikasi',
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
        ];

        foreach ($configs as $config) {
            SystemConfig::create($config);
        }

        // Create additional random configs
        SystemConfig::factory()->count(5)->create();
    }
} 