<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use App\Models\Borrow;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create regular users
        $users = User::factory()
            ->count(10)
            ->create();

        // Create categories
        $categories = Category::factory()
            ->count(8)
            ->create();

        // Create items and attach random categories
        $items = Item::factory()
            ->count(30)
            ->create()
            ->each(function ($item) use ($categories) {
                $item->categories()->attach(
                    $categories->random(rand(1, 3))->pluck('id')->toArray()
                );
            });

        // Create some items with low stock
        $lowStockItems = Item::factory()
            ->count(5)
            ->lowStock()
            ->create()
            ->each(function ($item) use ($categories) {
                $item->categories()->attach(
                    $categories->random(rand(1, 3))->pluck('id')->toArray()
                );
            });

        // Combine all items
        $allItems = $items->merge($lowStockItems);

        // Create active borrows
        Borrow::factory()
            ->count(15)
            ->state(function () use ($users, $allItems) {
                $item = $allItems->random();
                return [
                    'user_id' => $users->random()->id,
                    'item_id' => $item->id,
                    'quantity' => fake()->numberBetween(1, min(3, $item->quantity))
                ];
            })
            ->create();

        // Create returned borrows
        Borrow::factory()
            ->count(20)
            ->state(function () use ($users, $allItems) {
                $item = $allItems->random();
                return [
                    'user_id' => $users->random()->id,
                    'item_id' => $item->id,
                    'quantity' => fake()->numberBetween(1, min(3, $item->quantity))
                ];
            })
            ->returned()
            ->create();

        // Create overdue borrows
        Borrow::factory()
            ->count(5)
            ->state(function () use ($users, $allItems) {
                $item = $allItems->random();
                return [
                    'user_id' => $users->random()->id,
                    'item_id' => $item->id,
                    'quantity' => fake()->numberBetween(1, min(3, $item->quantity))
                ];
            })
            ->overdue()
            ->create();
    }
} 