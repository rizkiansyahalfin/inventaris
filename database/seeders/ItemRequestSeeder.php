<?php

namespace Database\Seeders;

use App\Models\ItemRequest;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ItemRequestSeeder extends Seeder
{
    public function run(): void
    {
        $santri = User::where('role', 'user')->get();
        $staff = User::whereIn('role', ['admin', 'petugas'])->get();
        $categories = Category::all();

        if ($santri->isEmpty() || $categories->isEmpty()) {
            $this->command->info('Tidak ada santri atau kategori yang tersedia untuk dibuatkan data permintaan barang.');
            return;
        }

        // Create realistic item requests
        $this->createKitabRequests($santri, $categories);
        $this->createAtkRequests($santri, $categories);
        $this->createElektronikRequests($staff, $categories);
        $this->createFurniturRequests($staff, $categories);
        $this->createKebersihanRequests($staff, $categories);
    }

    private function createKitabRequests($santri, $categories)
    {
        $kitabCategory = $categories->where('code', 'KTB')->first();
        
        $kitabRequests = [
            ['name' => 'Kitab Riyadhus Shalihin', 'quantity' => 10, 'reason' => 'Untuk pembelajaran hadits'],
            ['name' => 'Kitab Bulughul Maram', 'quantity' => 8, 'reason' => 'Untuk pembelajaran fiqih'],
            ['name' => 'Kitab Aqidatul Awwam', 'quantity' => 12, 'reason' => 'Untuk pembelajaran aqidah'],
            ['name' => 'Al-Quran Juz Amma', 'quantity' => 15, 'reason' => 'Untuk pembelajaran tahfidz'],
        ];

        foreach ($kitabRequests as $request) {
            $this->createRequestRecord($santri->random(), $kitabCategory, $request);
        }
    }

    private function createAtkRequests($santri, $categories)
    {
        $atkCategory = $categories->where('code', 'ATK')->first();
        
        $atkRequests = [
            ['name' => 'Buku Tulis A4', 'quantity' => 100, 'reason' => 'Stok buku tulis habis'],
            ['name' => 'Pulpen Pilot', 'quantity' => 50, 'reason' => 'Untuk kegiatan belajar mengajar'],
            ['name' => 'Kertas HVS A4', 'quantity' => 10, 'reason' => 'Untuk fotokopi materi pelajaran'],
            ['name' => 'Spidol Papan Tulis', 'quantity' => 20, 'reason' => 'Untuk keperluan mengajar'],
        ];

        foreach ($atkRequests as $request) {
            $this->createRequestRecord($santri->random(), $atkCategory, $request);
        }
    }

    private function createElektronikRequests($staff, $categories)
    {
        $elektronikCategory = $categories->where('code', 'ELK')->first();
        
        $elektronikRequests = [
            ['name' => 'Kipas Angin Standing', 'quantity' => 5, 'reason' => 'Untuk ruang belajar yang panas'],
            ['name' => 'Lampu LED 10W', 'quantity' => 30, 'reason' => 'Pengganti lampu yang rusak'],
            ['name' => 'Speaker Bluetooth', 'quantity' => 2, 'reason' => 'Untuk kegiatan pengajian'],
        ];

        foreach ($elektronikRequests as $request) {
            $this->createRequestRecord($staff->random(), $elektronikCategory, $request);
        }
    }

    private function createFurniturRequests($staff, $categories)
    {
        $furniturCategory = $categories->where('code', 'FRN')->first();
        
        $furniturRequests = [
            ['name' => 'Meja Belajar', 'quantity' => 10, 'reason' => 'Untuk santri baru'],
            ['name' => 'Kursi Plastik', 'quantity' => 20, 'reason' => 'Pengganti kursi yang rusak'],
            ['name' => 'Lemari Buku', 'quantity' => 3, 'reason' => 'Untuk perpustakaan'],
        ];

        foreach ($furniturRequests as $request) {
            $this->createRequestRecord($staff->random(), $furniturCategory, $request);
        }
    }

    private function createKebersihanRequests($staff, $categories)
    {
        $kebersihanCategory = $categories->where('code', 'KBR')->first();
        
        $kebersihanRequests = [
            ['name' => 'Sapu Lantai', 'quantity' => 10, 'reason' => 'Untuk kebersihan asrama'],
            ['name' => 'Sabun Cuci Piring', 'quantity' => 15, 'reason' => 'Stok sabun habis'],
            ['name' => 'Pembersih Lantai', 'quantity' => 8, 'reason' => 'Untuk kebersihan masjid'],
        ];

        foreach ($kebersihanRequests as $request) {
            $this->createRequestRecord($staff->random(), $kebersihanCategory, $request);
        }
    }

    private function createRequestRecord($user, $category, $requestData)
    {
        $statuses = ['pending', 'approved', 'rejected'];
        $status = $statuses[array_rand($statuses)];
        
        $requestDate = Carbon::now()->subDays(rand(1, 60));
        $approvedAt = $status === 'approved' ? $requestDate->copy()->addDays(rand(1, 3)) : null;

        // Get admin user safely
        $adminUser = User::where('role', 'admin')->first();

        ItemRequest::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => $requestData['name'],
            'quantity' => $requestData['quantity'],
            'reason' => $requestData['reason'],
            'description' => 'Permintaan barang: ' . $requestData['name'] . ' - ' . $requestData['reason'],
            'status' => $status,
            'approved_at' => $approvedAt,
            'approved_by' => $status === 'approved' && $adminUser ? $adminUser->id : null,
            'created_at' => $requestDate,
            'updated_at' => $requestDate,
        ]);
    }
} 