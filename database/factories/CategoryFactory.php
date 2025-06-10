<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $categories = [
            'Elektronik', 'Furnitur', 'Alat Tulis Kantor', 'Komputer & Aksesoris',
            'Proyektor & Layar', 'Peralatan Kebersihan', 'Peralatan Dapur', 'Buku & Dokumen'
        ];
        
        return [
            'name' => $this->faker->unique()->randomElement($categories),
            'description' => fake()->sentence(),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return fake()->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }
} 