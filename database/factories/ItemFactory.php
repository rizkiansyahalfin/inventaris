<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        $itemNames = [
            'Laptop Dell XPS 15', 'MacBook Pro 16-inch', 'Monitor LG UltraWide 34"', 'Keyboard Mechanical Keychron K2',
            'Mouse Logitech MX Master 3', 'Proyektor Epson EB-S41', 'Meja Kerja Adjustable', 'Kursi Ergonomis Herman Miller',
            'Printer HP LaserJet Pro', 'Kamera Sony A7 III', 'Speaker Bluetooth JBL', 'Papan Tulis Kaca',
            'AC Daikin 1 PK', 'Kulkas Sharp 2 Pintu', 'Dispenser Air Miyako', 'Mesin Kopi Nespresso',
            'Router Wi-Fi TP-Link Archer', 'Hard Disk Eksternal Seagate 2TB', 'Webcam Logitech C920',
            'Headset Sony WH-1000XM4', 'Tablet Samsung Galaxy Tab S8', 'Smartphone iPhone 14 Pro',
            'Lemari Arsip 4 Laci', 'Rak Server 42U', 'Scanner Fujitsu ScanSnap', 'Penghancur Kertas',
            'Telepon IP Cisco', 'Lampu Meja LED Philips', 'Genset Honda EU22i', 'UPS APC Back-UPS Pro',
            'Smart TV Samsung 55"', 'Microsoft Surface Pro 8'
        ];
        
        $conditions = ['Baik', 'Rusak Ringan', 'Rusak Berat'];
        $locations = ['Gudang A', 'Ruang Rapat 1', 'Lantai 2', 'Area Produksi', 'Kantor Pemasaran'];

        return [
            'name' => $this->faker->unique()->randomElement($itemNames),
            'qr_code' => null,
            'image' => null,
            'description' => fake()->paragraph(),
            'condition' => fake()->randomElement($conditions),
            'location' => fake()->randomElement($locations),
            'purchase_price' => fake()->optional()->randomFloat(2, 100000, 25000000),
            'purchase_date' => fake()->optional()->dateTimeBetween('-2 years', 'now'),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return fake()->dateTimeBetween($attributes['created_at'], 'now');
            },
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