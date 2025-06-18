<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Maintenance;
use App\Models\User;
use App\Models\Item;
use Carbon\Carbon;

class MaintenanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $staff = User::whereIn('role', ['admin', 'petugas'])->get();
        $items = Item::all();

        if ($staff->isEmpty() || $items->isEmpty()) {
            $this->command->info('Tidak ada petugas/admin atau barang yang tersedia untuk dibuatkan data pemeliharaan.');
            return;
        }

        // Create realistic maintenance scenarios
        $this->createElektronikMaintenance($staff, $items);
        $this->createFurniturMaintenance($staff, $items);
        $this->createKomputerMaintenance($staff, $items);
        $this->createProyektorMaintenance($staff, $items);
        $this->createDapurMaintenance($staff, $items);
        $this->createKebersihanMaintenance($staff, $items);
    }

    private function createElektronikMaintenance($staff, $items)
    {
        $elektronikItems = $items->where('category.code', 'ELK');
        
        foreach ($elektronikItems as $item) {
            // 30% chance of needing maintenance
            if (rand(1, 100) <= 30) {
                $maintenanceType = $this->getMaintenanceType($item->condition);
                $startDate = Carbon::now()->subDays(rand(1, 180));
                $duration = $this->getMaintenanceDuration($maintenanceType);
                
                $this->createMaintenanceRecord($staff->random(), $item, $maintenanceType, $startDate, $duration);
            }
        }
    }

    private function createFurniturMaintenance($staff, $items)
    {
        $furniturItems = $items->where('category.code', 'FRN');
        
        foreach ($furniturItems as $item) {
            // 25% chance of needing maintenance
            if (rand(1, 100) <= 25) {
                $maintenanceType = $this->getMaintenanceType($item->condition);
                $startDate = Carbon::now()->subDays(rand(1, 120));
                $duration = $this->getMaintenanceDuration($maintenanceType);
                
                $this->createMaintenanceRecord($staff->random(), $item, $maintenanceType, $startDate, $duration);
            }
        }
    }

    private function createKomputerMaintenance($staff, $items)
    {
        $komputerItems = $items->where('category.code', 'KMP');
        
        foreach ($komputerItems as $item) {
            // 40% chance of needing maintenance
            if (rand(1, 100) <= 40) {
                $maintenanceType = $this->getMaintenanceType($item->condition);
                $startDate = Carbon::now()->subDays(rand(1, 90));
                $duration = $this->getMaintenanceDuration($maintenanceType);
                
                $this->createMaintenanceRecord($staff->random(), $item, $maintenanceType, $startDate, $duration);
            }
        }
    }

    private function createProyektorMaintenance($staff, $items)
    {
        $proyektorItems = $items->where('category.code', 'PRJ');
        
        foreach ($proyektorItems as $item) {
            // 35% chance of needing maintenance
            if (rand(1, 100) <= 35) {
                $maintenanceType = $this->getMaintenanceType($item->condition);
                $startDate = Carbon::now()->subDays(rand(1, 60));
                $duration = $this->getMaintenanceDuration($maintenanceType);
                
                $this->createMaintenanceRecord($staff->random(), $item, $maintenanceType, $startDate, $duration);
            }
        }
    }

    private function createDapurMaintenance($staff, $items)
    {
        $dapurItems = $items->where('category.code', 'DKP');
        
        foreach ($dapurItems as $item) {
            // 20% chance of needing maintenance
            if (rand(1, 100) <= 20) {
                $maintenanceType = $this->getMaintenanceType($item->condition);
                $startDate = Carbon::now()->subDays(rand(1, 150));
                $duration = $this->getMaintenanceDuration($maintenanceType);
                
                $this->createMaintenanceRecord($staff->random(), $item, $maintenanceType, $startDate, $duration);
            }
        }
    }

    private function createKebersihanMaintenance($staff, $items)
    {
        $kebersihanItems = $items->where('category.code', 'KBR');
        
        foreach ($kebersihanItems as $item) {
            // 15% chance of needing maintenance
            if (rand(1, 100) <= 15) {
                $maintenanceType = $this->getMaintenanceType($item->condition);
                $startDate = Carbon::now()->subDays(rand(1, 200));
                $duration = $this->getMaintenanceDuration($maintenanceType);
                
                $this->createMaintenanceRecord($staff->random(), $item, $maintenanceType, $startDate, $duration);
            }
        }
    }

    private function getMaintenanceType($condition)
    {
        $types = ['Perawatan', 'Perbaikan', 'Penggantian'];
        
        switch ($condition) {
            case 'Baik':
                return 'Perawatan';
            case 'Rusak Ringan':
                return 'Perbaikan';
            case 'Rusak Sedang':
                return 'Perbaikan';
            case 'Rusak Berat':
                return 'Penggantian';
            default:
                return $types[array_rand($types)];
        }
    }

    private function getMaintenanceDuration($type)
    {
        switch ($type) {
            case 'Perawatan':
                return rand(1, 3);
            case 'Perbaikan':
                return rand(2, 7);
            case 'Penggantian':
                return rand(1, 5);
            default:
                return rand(1, 5);
        }
    }

    private function createMaintenanceRecord($user, $item, $type, $startDate, $duration)
    {
        $titles = [
            'Perawatan' => 'Pemeliharaan Rutin ' . $item->name,
            'Perbaikan' => 'Perbaikan ' . $item->name,
            'Penggantian' => 'Penggantian ' . $item->name,
        ];

        $notes = [
            'Perawatan' => 'Pemeliharaan rutin untuk menjaga kondisi ' . $item->name . ' agar tetap optimal.',
            'Perbaikan' => 'Perbaikan ' . $item->name . ' yang mengalami kerusakan ringan.',
            'Penggantian' => 'Penggantian ' . $item->name . ' yang sudah tidak dapat diperbaiki.',
        ];

        $costs = [
            'Perawatan' => rand(50000, 200000),
            'Perbaikan' => rand(100000, 500000),
            'Penggantian' => rand(200000, 1000000),
        ];

        Maintenance::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'type' => $type,
            'title' => $titles[$type],
            'notes' => $notes[$type],
            'cost' => $costs[$type],
            'start_date' => $startDate,
            'completion_date' => $startDate->copy()->addDays($duration),
        ]);
    }
} 