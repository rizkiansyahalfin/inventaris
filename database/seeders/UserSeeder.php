<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin users (Pengurus Pondok)
        $admins = [
            [
                'name' => 'Ustadz Ahmad Fauzi',
                'email' => 'admin@pondok.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
            ],
            [
                'name' => 'Ustadz Muhammad Rizki',
                'email' => 'rizki@pondok.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
            ],
        ];

        foreach ($admins as $admin) {
            if (!User::where('email', $admin['email'])->exists()) {
                User::create($admin);
            }
        }

        // Create petugas users (Staff Pondok)
        $petugas = [
            [
                'name' => 'Pak Soleh',
                'email' => 'soleh@pondok.com',
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'status' => 'active',
            ],
            [
                'name' => 'Pak Hadi',
                'email' => 'hadi@pondok.com',
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'status' => 'active',
            ],
            [
                'name' => 'Bu Siti',
                'email' => 'siti@pondok.com',
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'status' => 'active',
            ],
        ];

        foreach ($petugas as $staff) {
            if (!User::where('email', $staff['email'])->exists()) {
                User::create($staff);
            }
        }

        // Create santri users (Students)
        $santri = [
            ['name' => 'Ahmad Fadillah', 'email' => 'ahmad.fadillah@pondok.com'],
            ['name' => 'Muhammad Rizki', 'email' => 'muhammad.rizki@pondok.com'],
            ['name' => 'Abdullah Rahman', 'email' => 'abdullah.rahman@pondok.com'],
            ['name' => 'Hasan Basri', 'email' => 'hasan.basri@pondok.com'],
            ['name' => 'Ali Mustafa', 'email' => 'ali.mustafa@pondok.com'],
            ['name' => 'Umar Faruq', 'email' => 'umar.faruq@pondok.com'],
            ['name' => 'Usman bin Affan', 'email' => 'usman.affan@pondok.com'],
            ['name' => 'Fatimah Azzahra', 'email' => 'fatimah.azzahra@pondok.com'],
            ['name' => 'Aisyah binti Abu Bakar', 'email' => 'aisyah.abubakar@pondok.com'],
            ['name' => 'Khadijah binti Khuwailid', 'email' => 'khadijah.khuwailid@pondok.com'],
        ];

        foreach ($santri as $student) {
            if (!User::where('email', $student['email'])->exists()) {
                User::create([
                    'name' => $student['name'],
                    'email' => $student['email'],
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'status' => 'active',
                ]);
            }
        }

        // Create additional random santri if needed
        $randomUserCount = User::where('role', 'user')->count();
        if ($randomUserCount < 20) {
            User::factory()->user()->count(20 - $randomUserCount)->create();
        }
    }
}
