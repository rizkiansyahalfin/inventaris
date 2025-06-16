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

        $maintenanceTypes = ['Perawatan', 'Perbaikan', 'Penggantian'];

        for ($i = 0; $i < 20; $i++) {
            $item = $items->random();
            $user = $staff->random();
            $startDate = Carbon::now()->subDays(rand(1, 180));
            
            // Determine valid maintenance type based on item condition
            $validTypes = $maintenanceTypes;
            if ($item->condition === 'Baik') {
                $validTypes = ['Perawatan', 'Penggantian'];
            }
            $selectedType = $validTypes[array_rand($validTypes)];

            Maintenance::create([
                'item_id' => $item->id,
                'user_id' => $user->id,
                'type' => $selectedType,
                'title' => 'Pemeliharaan Rutin ' . $item->name,
                'notes' => 'Catatan pemeliharaan dummy untuk ' . $item->name . '.',
                'cost' => rand(25000, 500000),
                'start_date' => $startDate,
                'completion_date' => $startDate->copy()->addDays(rand(1, 5)),
            ]);
        }
    }
} 