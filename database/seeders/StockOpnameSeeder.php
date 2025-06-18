<?php

namespace Database\Seeders;

use App\Models\StockOpname;
use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StockOpnameSeeder extends Seeder
{
    public function run(): void
    {
        $staff = User::whereIn('role', ['admin', 'petugas'])->get();
        $items = Item::all();

        if ($staff->isEmpty() || $items->isEmpty()) {
            $this->command->info('Tidak ada petugas/admin atau barang yang tersedia untuk dibuatkan data stock opname.');
            return;
        }

        // Create stock opnames for the last 6 months
        for ($month = 1; $month <= 6; $month++) {
            $this->createMonthlyStockOpname($staff->random(), $items, $month);
        }
    }

    private function createMonthlyStockOpname($user, $items, $monthOffset)
    {
        $startDate = Carbon::now()->subMonths($monthOffset)->startOfMonth();
        $endDate = Carbon::now()->subMonths($monthOffset)->endOfMonth();
        
        // Random status with weighted probability
        $statuses = ['pending', 'in_progress', 'completed'];
        $status = $statuses[array_rand($statuses)];
        
        // Generate realistic notes based on status
        $notes = $this->generateNotes($status, $items);

        StockOpname::create([
            'name' => 'Stock Opname ' . $startDate->format('F Y'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status,
            'notes' => $notes,
            'created_by' => $user->id,
            'created_at' => $startDate,
            'updated_at' => $endDate,
        ]);
    }

    private function generateNotes($status, $items)
    {
        switch ($status) {
            case 'pending':
                return 'Stock opname belum dimulai. Menunggu jadwal pelaksanaan.';
            
            case 'in_progress':
                return 'Stock opname sedang berlangsung. Tim sedang melakukan pengecekan fisik barang.';
            
            case 'completed':
                $discrepancyCount = rand(0, 5);
                if ($discrepancyCount === 0) {
                    return 'Stock opname selesai. Semua barang sesuai dengan sistem.';
                } else {
                    $sampleItems = $items->random(min(3, $discrepancyCount));
                    $discrepancies = [];
                    foreach ($sampleItems as $item) {
                        $systemStock = $item->stock;
                        $physicalStock = $systemStock + rand(-3, 3);
                        if ($physicalStock < 0) $physicalStock = 0;
                        $discrepancies[] = "{$item->name} (Sistem: {$systemStock}, Fisik: {$physicalStock})";
                    }
                    return 'Stock opname selesai. Ditemukan ' . $discrepancyCount . ' perbedaan: ' . implode(', ', $discrepancies);
                }
            
            default:
                return 'Stock opname rutin bulanan.';
        }
    }
}