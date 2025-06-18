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
            SystemConfig::firstOrCreate(
                ['key' => $config['key']],
                $config
            );
        }

        // Create additional random configs only if they don't exist
        $existingKeys = SystemConfig::pluck('key')->toArray();
        $neededCount = 10 - count($existingKeys);
        
        if ($neededCount > 0) {
            for ($i = 0; $i < $neededCount; $i++) {
                do {
                    $key = fake()->unique()->word();
                } while (in_array($key, $existingKeys));
                
                $existingKeys[] = $key;
                
                SystemConfig::create([
                    'key' => $key,
                    'value' => fake()->sentence(),
                    'description' => fake()->sentence(),
                ]);
            }
        }
    }
} 