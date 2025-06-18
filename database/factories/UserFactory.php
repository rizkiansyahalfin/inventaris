<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->generatePondokName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => fake()->randomElement(['admin', 'petugas', 'user']),
            'status' => 'active',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'name' => 'Ustadz ' . $this->generatePondokName(),
        ]);
    }

    public function petugas(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'petugas',
            'name' => $this->generatePondokName(),
        ]);
    }

    public function user(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'user',
            'name' => $this->generatePondokName(),
        ]);
    }

    private function generatePondokName(): string
    {
        $maleNames = [
            'Ahmad', 'Muhammad', 'Abdullah', 'Hasan', 'Ali', 'Umar', 'Usman', 'Fatih', 'Rizki', 'Fadillah',
            'Rahman', 'Basri', 'Mustafa', 'Faruq', 'Affan', 'Ibrahim', 'Yusuf', 'Zakaria', 'Harun', 'Musa',
            'Isa', 'Daud', 'Sulaiman', 'Yahya', 'Yunus', 'Ayyub', 'Syuaib', 'Hud', 'Saleh', 'Lut'
        ];

        $femaleNames = [
            'Fatimah', 'Aisyah', 'Khadijah', 'Zainab', 'Ruqayyah', 'Ummu Kulthum', 'Hafsah', 'Zainab', 'Safiyyah',
            'Juwairiyah', 'Ummu Habibah', 'Maimunah', 'Zainab binti Jahsy', 'Ummu Salamah', 'Hindun', 'Asma',
            'Ummu Aiman', 'Barakah', 'Ummu Haram', 'Ummu Sulaim', 'Ummu Atiyyah', 'Ummu Waraqah'
        ];

        $surnames = [
            'Al-Fauzi', 'Ar-Rizki', 'Al-Basri', 'Al-Mustafa', 'Al-Faruq', 'Al-Affan', 'Az-Zahra', 'Binti Abu Bakar',
            'Binti Khuwailid', 'Binti Jahsy', 'Binti Jahsy', 'Al-Qurasy', 'Al-Ansari', 'Al-Muhajir', 'Al-Madani'
        ];

        $isMale = fake()->boolean(70); // 70% chance of male names
        $firstName = $isMale ? fake()->randomElement($maleNames) : fake()->randomElement($femaleNames);
        $surname = fake()->randomElement($surnames);

        return $firstName . ' ' . $surname;
    }
}
