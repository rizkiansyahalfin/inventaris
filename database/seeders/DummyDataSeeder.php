<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use App\Models\Borrow;
use App\Models\ItemRequest;
use App\Models\Maintenance;
use App\Models\StockOpname;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Membuat data dummy untuk testing...');

        // Create additional santri users
        $additionalSantri = User::factory()
            ->user()
            ->count(15)
            ->create();

        // Create additional staff
        $additionalStaff = User::factory()
            ->petugas()
            ->count(3)
            ->create();

        // Get existing categories
        $categories = Category::all();

        // Create additional items for testing
        $additionalItems = $this->createAdditionalItems($categories);

        // Create additional borrows
        $this->createAdditionalBorrows($additionalSantri, $additionalItems);

        // Create additional item requests
        $this->createAdditionalRequests($additionalSantri, $additionalStaff, $categories);

        // Create additional maintenance records
        $this->createAdditionalMaintenance($additionalStaff, $additionalItems);

        // Create additional stock opnames
        $this->createAdditionalStockOpnames($additionalStaff, $additionalItems);


        $this->command->info('Data dummy berhasil dibuat!');
    }

    private function createAdditionalItems($categories)
    {
        $additionalItems = [];

        // Additional Kitab items
        $kitabCategory = $categories->where('code', 'KTB')->first();
        if ($kitabCategory) {
            $additionalItems[] = Item::create([
                'name' => 'Kitab Ihya Ulumuddin',
                'code' => 'KTB/' . Carbon::now()->format('ym') . '/009',
                'description' => 'Kitab klasik tentang tasawuf dan akhlak',
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'location' => 'Perpustakaan',
                'purchase_price' => 75000,
                'purchase_date' => Carbon::now()->subMonths(2),
                'category_id' => $kitabCategory->id,
                'stock' => 5,
                'unit' => 'eksemplar',
                'minimum_stock' => 2,
            ]);

            $additionalItems[] = Item::create([
                'name' => 'Kitab Bidayatul Hidayah',
                'code' => 'KTB/' . Carbon::now()->format('ym') . '/010',
                'description' => 'Kitab tentang adab dan akhlak',
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'location' => 'Perpustakaan',
                'purchase_price' => 45000,
                'purchase_date' => Carbon::now()->subMonths(1),
                'category_id' => $kitabCategory->id,
                'stock' => 8,
                'unit' => 'eksemplar',
                'minimum_stock' => 3,
            ]);
        }

        // Additional ATK items
        $atkCategory = $categories->where('code', 'ATK')->first();
        if ($atkCategory) {
            $additionalItems[] = Item::create([
                'name' => 'Tipe-X',
                'code' => 'ATK/' . Carbon::now()->format('ym') . '/009',
                'description' => 'Peralatan tulis untuk menghapus kesalahan',
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'location' => 'Gudang ATK',
                'purchase_price' => 8000,
                'purchase_date' => Carbon::now()->subWeeks(2),
                'category_id' => $atkCategory->id,
                'stock' => 25,
                'unit' => 'pcs',
                'minimum_stock' => 10,
            ]);
        }

        // Additional Elektronik items
        $elektronikCategory = $categories->where('code', 'ELK')->first();
        if ($elektronikCategory) {
            $additionalItems[] = Item::create([
                'name' => 'Kabel HDMI 2m',
                'code' => 'ELK/' . Carbon::now()->format('ym') . '/007',
                'description' => 'Kabel HDMI untuk koneksi audio video',
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'location' => 'Gudang Elektronik',
                'purchase_price' => 85000,
                'purchase_date' => Carbon::now()->subWeeks(1),
                'category_id' => $elektronikCategory->id,
                'stock' => 8,
                'unit' => 'pcs',
                'minimum_stock' => 3,
            ]);
        }

        return collect($additionalItems);
    }

    private function createAdditionalBorrows($santri, $items)
    {
        foreach ($items as $item) {
            // Create some borrows for each item
            $borrowCount = rand(1, 3);
            
            for ($i = 0; $i < $borrowCount; $i++) {
                $user = $santri->random();
                $borrowDate = Carbon::now()->subDays(rand(1, 45));
                $dueDate = $borrowDate->copy()->addDays(rand(7, 14));
                
                // 70% chance of being returned
                $isReturned = rand(1, 100) <= 70;
                $status = $isReturned ? 'returned' : 'borrowed';
                $returnDate = $isReturned ? $dueDate->copy()->subDays(rand(0, 2)) : null;

                Borrow::create([
                    'user_id' => $user->id,
                    'item_id' => $item->id,
                    'quantity' => rand(1, 2),
                    'borrow_date' => $borrowDate,
                    'due_date' => $dueDate,
                    'return_date' => $returnDate,
                    'status' => $status,
                    'condition_at_borrow' => $item->condition,
                    'condition_on_return' => $status === 'returned' ? $item->condition : null,
                    'notes' => 'Peminjaman untuk keperluan belajar',
                    'approved_by' => User::where('role', 'admin')->first()->id,
                    'approved_at' => $borrowDate,
                ]);
            }
        }
    }

    private function createAdditionalRequests($santri, $staff, $categories)
    {
        $requestData = [
            ['name' => 'Kitab Al-Hikam', 'quantity' => 5, 'reason' => 'Untuk pembelajaran tasawuf'],
            ['name' => 'Buku Tulis Folio', 'quantity' => 80, 'reason' => 'Stok buku tulis menipis'],
            ['name' => 'Kipas Angin Meja', 'quantity' => 3, 'reason' => 'Untuk ruang belajar'],
            ['name' => 'Rak Sepatu', 'quantity' => 5, 'reason' => 'Untuk asrama santri baru'],
        ];

        foreach ($requestData as $request) {
            $user = rand(1, 100) <= 70 ? $santri->random() : $staff->random();
            $category = $categories->random();
            $status = ['pending', 'approved', 'rejected'][array_rand(['pending', 'approved', 'rejected'])];
            
            $requestDate = Carbon::now()->subDays(rand(1, 30));
            $approvedAt = $status === 'approved' ? $requestDate->copy()->addDays(rand(1, 3)) : null;
            $rejectedAt = $status === 'rejected' ? $requestDate->copy()->addDays(rand(1, 2)) : null;

            ItemRequest::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'name' => $request['name'],
                'quantity' => $request['quantity'],
                'reason' => $request['reason'],
                'description' => 'Permintaan barang: ' . $request['name'] . ' - ' . $request['reason'],
                'status' => $status,
                'approved_at' => $approvedAt,
                'approved_by' => $status === 'approved' ? User::where('role', 'admin')->first()->id : null,
                'created_at' => $requestDate,
                'updated_at' => $requestDate,
            ]);
        }
    }

    private function createAdditionalMaintenance($staff, $items)
    {
        foreach ($items as $item) {
            // 30% chance of needing maintenance
            if (rand(1, 100) <= 30) {
                $maintenanceType = ['Perawatan', 'Perbaikan', 'Penggantian'][array_rand(['Perawatan', 'Perbaikan', 'Penggantian'])];
                $startDate = Carbon::now()->subDays(rand(1, 60));
                $duration = rand(1, 5);

                Maintenance::create([
                    'item_id' => $item->id,
                    'user_id' => $staff->random()->id,
                    'type' => $maintenanceType,
                    'title' => $maintenanceType . ' ' . $item->name,
                    'notes' => 'Pemeliharaan rutin untuk ' . $item->name,
                    'cost' => rand(50000, 300000),
                    'start_date' => $startDate,
                    'completion_date' => $startDate->copy()->addDays($duration),
                ]);
            }
        }
    }

    private function createAdditionalStockOpnames($staff, $items)
    {
        // Create stock opnames for the last 3 months
        for ($month = 1; $month <= 3; $month++) {
            $opnameDate = Carbon::now()->subMonths($month)->endOfMonth();
            $user = $staff->random();
            
            $discrepancyCount = rand(0, 3);
            $status = $discrepancyCount === 0 ? 'completed' : ($discrepancyCount <= 2 ? 'in_progress' : 'pending');

            StockOpname::create([
                'name' => 'Stock Opname Tambahan - ' . $opnameDate->format('F Y'),
                'start_date' => $opnameDate->copy()->startOfMonth(),
                'end_date' => $opnameDate,
                'status' => $status,
                'notes' => $discrepancyCount === 0 ? 'Semua barang sesuai' : 'Ditemukan beberapa perbedaan minor',
                'created_by' => $user->id,
                'created_at' => $opnameDate,
                'updated_at' => $opnameDate,
            ]);
        }
    }
}