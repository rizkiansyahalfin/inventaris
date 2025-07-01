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
        $name = $this->generatePondokItemName();
        $code = strtoupper(Str::substr(Str::slug($name), 0, 6));
        
        // Check if code exists and generate a new one if it does
        while (Item::where('code', $code)->exists()) {
            $name = $this->generatePondokItemName();
            $code = strtoupper(Str::substr(Str::slug($name), 0, 6));
        }

        return [
            'name' => $name,
            'code' => $code,
            'qr_code' => null,
            'image' => null,
            'description' => 'Peralatan pondok pesantren - ' . $name,
            'condition' => fake()->randomElement(['Baik', 'Rusak Ringan', 'Rusak Sedang']),
            'status' => fake()->randomElement(['Tersedia', 'Dipinjam', 'Dalam Perbaikan']),
            'location_id' => \App\Models\Location::inRandomOrder()->value('id'),
            'purchase_price' => fake()->optional()->randomFloat(2, 10000, 5000000),
            'purchase_date' => fake()->optional()->dateTimeBetween('-2 years', 'now'),
            'category_id' => Category::factory(),
            'stock' => fake()->numberBetween(1, 50),
            'unit' => fake()->randomElement(['pcs', 'unit', 'box', 'pack', 'eksemplar', 'set', 'roll', 'botol']),
            'minimum_stock' => fake()->numberBetween(1, 10),
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

    /**
     * Indicate that the item is available.
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Tersedia',
        ]);
    }

    private function generatePondokItemName(): string
    {
        $kitabItems = [
            'Kitab Safinatun Najah', 'Kitab Ta\'limul Muta\'allim', 'Kitab Fathul Qarib', 'Kitab Fathul Mu\'in',
            'Kitab Riyadhus Shalihin', 'Kitab Bulughul Maram', 'Kitab Aqidatul Awwam', 'Kitab Al-Hikam',
            'Kitab Bidayatul Hidayah', 'Kitab Ihya Ulumuddin', 'Al-Quran Mushaf Utsmani', 'Al-Quran Juz Amma',
            'Buku Hadits Arba\'in', 'Buku Aqidah Ahlus Sunnah', 'Buku Fiqih Ibadah'
        ];

        $atkItems = [
            'Pulpen Pilot', 'Buku Tulis A4', 'Pensil 2B', 'Penghapus', 'Penggaris 30cm', 'Spidol Papan Tulis',
            'Kertas HVS A4', 'Stapler', 'Tipe-X', 'Buku Tulis Folio', 'Map Plastik', 'Amplop'
        ];

        $elektronikItems = [
            'Kipas Angin Standing', 'Lampu LED 10W', 'Kabel Listrik 10m', 'Stop Kontak 4 Lubang',
            'Charger HP Universal', 'Speaker Bluetooth', 'Kipas Angin Meja', 'Kabel HDMI 2m',
            'Adaptor 12V', 'Baterai AA', 'Baterai AAA'
        ];

        $furniturItems = [
            'Meja Belajar', 'Kursi Plastik', 'Lemari Buku', 'Rak Sepatu', 'Tempat Tidur', 'Kasur Busa',
            'Meja Makan', 'Kursi Kayu', 'Lemari Pakaian', 'Rak Buku', 'Meja Rapat', 'Papan Tulis'
        ];

        $komputerItems = [
            'Laptop Asus', 'Printer Epson L120', 'Mouse Wireless', 'Keyboard USB', 'Flashdisk 16GB',
            'Monitor LCD', 'Speaker Komputer', 'Webcam', 'Scanner', 'UPS'
        ];

        $proyektorItems = [
            'Proyektor Epson', 'Layar Proyektor 2x3m', 'Speaker Active', 'Microphone Wireless',
            'Kabel VGA', 'Remote Proyektor', 'Tripod Proyektor'
        ];

        $dapurItems = [
            'Rice Cooker 1.8L', 'Kompor Gas 2 Tungku', 'Panci Stainless', 'Wajan Anti Lengket',
            'Piring Melamin', 'Gelas Plastik', 'Sendok Garpu', 'Teko Air', 'Termos'
        ];

        $kebersihanItems = [
            'Sapu Lantai', 'Kemoceng', 'Ember Plastik', 'Pel Lantai', 'Sabun Cuci Piring',
            'Pembersih Lantai', 'Sikat WC', 'Kain Pel', 'Tissue'
        ];

        $olahragaItems = [
            'Bola Sepak', 'Bola Voli', 'Jaring Badminton', 'Raket Badminton', 'Shuttlecock',
            'Bola Basket', 'Net Voli', 'Tiang Badminton'
        ];

        $kesehatanItems = [
            'Kotak P3K', 'Perban Elastis', 'Betadine', 'Kapas', 'Termometer', 'Obat Pusing',
            'Obat Batuk', 'Minyak Kayu Putih'
        ];

        $tukangItems = [
            'Palu', 'Obeng Set', 'Tang Kombinasi', 'Gergaji Kayu', 'Meteran 5m', 'Bor Listrik',
            'Gerinda', 'Cat Tembok', 'Kuas Cat'
        ];

        $transportItems = [
            'Motor Honda Beat', 'Sepeda Onthel', 'Helm SNI', 'Jaket Motor', 'Sepeda Motor',
            'Mobil Pickup', 'Gerobak'
        ];

        $allItems = array_merge(
            $kitabItems, $atkItems, $elektronikItems, $furniturItems, $komputerItems,
            $proyektorItems, $dapurItems, $kebersihanItems, $olahragaItems, $kesehatanItems,
            $tukangItems, $transportItems
        );

        return fake()->randomElement($allItems);
    }

    private function generatePondokLocation(): string
    {
        $locations = [
            'Perpustakaan', 'Gudang ATK', 'Gudang Elektronik', 'Gudang Furnitur', 'Ruang Komputer',
            'Aula', 'Dapur', 'Gudang Kebersihan', 'Lapangan Olahraga', 'UKS', 'Bengkel', 'Garasi',
            'Asrama Putra', 'Asrama Putri', 'Ruang Makan', 'Ruang Rapat', 'Kantor Pengurus',
            'Masjid', 'Kamar Mandi', 'Gudang Utama'
        ];

        return fake()->randomElement($locations);
    }
}