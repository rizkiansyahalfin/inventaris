<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);
        $code = strtoupper(Str::substr(Str::slug($name), 0, 6));
        
        // Check if code exists and generate a new one if it does
        while (Item::where('code', $code)->exists()) {
            $name = fake()->words(3, true);
            $code = strtoupper(Str::substr(Str::slug($name), 0, 6));
        }

        return [
            'name' => $name,
            'code' => $code,
            'qr_code' => null,
            'image' => null,
            'description' => fake()->paragraph(),
            'condition' => 'Baik',
            'status' => fake()->randomElement(['active', 'inactive']),
            'location' => fake()->randomElement(['Gudang A', 'Ruang Rapat 1', 'Lantai 2', 'Area Produksi', 'Kantor Pemasaran']),
            'purchase_price' => fake()->optional()->randomFloat(2, 100000, 25000000),
            'purchase_date' => fake()->optional()->dateTimeBetween('-2 years', 'now'),
            'category_id' => Category::factory(),
            'stock' => fake()->numberBetween(0, 100),
            'unit' => fake()->randomElement(['pcs', 'unit', 'box', 'pack']),
            'minimum_stock' => fake()->numberBetween(5, 20),
        ];
    }

    /**
     * Indicate that the item is in good condition.
     */
    public function goodCondition(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => 'Baik',
        ]);
    }
} 