<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Item;
use App\Models\Borrow;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $items = Item::all();
        $borrows = Borrow::all();

        if ($users->isEmpty() || $items->isEmpty()) {
            $this->command->info('Tidak ada user atau barang yang tersedia untuk dibuatkan data aktivitas.');
            return;
        }

        // Create realistic activity logs
        $this->createItemActivities($users, $items);
        $this->createBorrowActivities($users, $borrows);
        $this->createSystemActivities($users);
        $this->createMaintenanceActivities($users, $items);
    }

    private function createItemActivities($users, $items)
    {
        $activities = [
            'Menambahkan barang baru',
            'Mengupdate informasi barang',
            'Mengubah status barang',
            'Mengubah lokasi barang',
            'Menghapus barang',
            'Mengupload foto barang',
            'Menggenerate QR Code',
            'Mengupdate stok barang',
        ];

        foreach ($items as $item) {
            // Create 2-5 activities per item
            $activityCount = rand(2, 5);
            
            for ($i = 0; $i < $activityCount; $i++) {
                $activity = $activities[array_rand($activities)];
                $user = $users->random();
                $timestamp = Carbon::now()->subDays(rand(1, 90));
                
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => $activity,
                    'module' => 'items',
                    'description' => $activity . ' - ' . $item->name,
                    'ip_address' => $this->generateRandomIP(),
                    'user_agent' => $this->generateRandomUserAgent(),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);
            }
        }
    }

    private function createBorrowActivities($users, $borrows)
    {
        $activities = [
            'Mengajukan peminjaman',
            'Menyetujui peminjaman',
            'Menolak peminjaman',
            'Mengembalikan barang',
            'Memperpanjang peminjaman',
            'Mencatat keterlambatan',
            'Mengirim pengingat',
        ];

        foreach ($borrows as $borrow) {
            // Create activities for each borrow
            $user = $users->random();
            $timestamp = $borrow->created_at;
            
            // Borrow request activity
            ActivityLog::create([
                'user_id' => $borrow->user_id,
                'action' => 'Mengajukan peminjaman',
                'module' => 'borrows',
                'description' => 'Mengajukan peminjaman ' . $borrow->item->name,
                'ip_address' => $this->generateRandomIP(),
                'user_agent' => $this->generateRandomUserAgent(),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            // Approval activity
            if ($borrow->approved_by) {
                ActivityLog::create([
                    'user_id' => $borrow->approved_by,
                    'action' => 'Menyetujui peminjaman',
                    'module' => 'borrows',
                    'description' => 'Menyetujui peminjaman ' . $borrow->item->name . ' oleh ' . $borrow->user->name,
                    'ip_address' => $this->generateRandomIP(),
                    'user_agent' => $this->generateRandomUserAgent(),
                    'created_at' => $borrow->approved_at,
                    'updated_at' => $borrow->approved_at,
                ]);
            }

            // Return activity
            if ($borrow->return_date) {
                ActivityLog::create([
                    'user_id' => $borrow->user_id,
                    'action' => 'Mengembalikan barang',
                    'module' => 'borrows',
                    'description' => 'Mengembalikan ' . $borrow->item->name,
                    'ip_address' => $this->generateRandomIP(),
                    'user_agent' => $this->generateRandomUserAgent(),
                    'created_at' => $borrow->return_date,
                    'updated_at' => $borrow->return_date,
                ]);
            }
        }
    }

    private function createSystemActivities($users)
    {
        $activities = [
            'Login ke sistem',
            'Logout dari sistem',
            'Mengubah password',
            'Mengupdate profil',
            'Mengakses dashboard',
            'Mengunduh laporan',
            'Mengatur konfigurasi sistem',
            'Backup database',
            'Restore database',
        ];

        // Create system activities for the last 30 days
        for ($day = 1; $day <= 30; $day++) {
            $dailyActivities = rand(10, 25);
            
            for ($i = 0; $i < $dailyActivities; $i++) {
                $activity = $activities[array_rand($activities)];
                $user = $users->random();
                $timestamp = Carbon::now()->subDays($day)->addHours(rand(0, 23))->addMinutes(rand(0, 59));
                
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => $activity,
                    'module' => 'system',
                    'description' => $activity . ' oleh ' . $user->name,
                    'ip_address' => $this->generateRandomIP(),
                    'user_agent' => $this->generateRandomUserAgent(),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);
            }
        }
    }

    private function createMaintenanceActivities($users, $items)
    {
        $activities = [
            'Menjadwalkan maintenance',
            'Memulai maintenance',
            'Menyelesaikan maintenance',
            'Mencatat biaya maintenance',
            'Mengupdate status maintenance',
        ];

        $maintenanceItems = $items->whereIn('category.code', ['ELK', 'KMP', 'PRJ', 'FRN']);
        
        foreach ($maintenanceItems as $item) {
            // 20% chance of having maintenance activities
            if (rand(1, 100) <= 20) {
                $user = $users->whereIn('role', ['admin', 'petugas'])->random();
                $timestamp = Carbon::now()->subDays(rand(1, 60));
                
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'Menjadwalkan maintenance',
                    'module' => 'maintenance',
                    'description' => 'Menjadwalkan maintenance untuk ' . $item->name,
                    'ip_address' => $this->generateRandomIP(),
                    'user_agent' => $this->generateRandomUserAgent(),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);
            }
        }
    }

    private function generateRandomIP()
    {
        return rand(192, 223) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 254);
    }

    private function generateRandomUserAgent()
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:89.0) Gecko/20100101 Firefox/89.0',
        ];
        
        return $userAgents[array_rand($userAgents)];
    }
}