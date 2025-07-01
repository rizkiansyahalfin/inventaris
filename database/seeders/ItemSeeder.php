<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;
use Carbon\Carbon;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Get categories
        $kitabCategory = Category::where('code', 'KTB')->first();
        $atkCategory = Category::where('code', 'ATK')->first();
        $elektronikCategory = Category::where('code', 'ELK')->first();
        $furniturCategory = Category::where('code', 'FRN')->first();
        $komputerCategory = Category::where('code', 'KMP')->first();
        $proyektorCategory = Category::where('code', 'PRJ')->first();
        $dapurCategory = Category::where('code', 'DKP')->first();
        $kebersihanCategory = Category::where('code', 'KBR')->first();
        $olahragaCategory = Category::where('code', 'OLG')->first();
        $kesehatanCategory = Category::where('code', 'KSH')->first();
        $tukangCategory = Category::where('code', 'TKN')->first();
        $transportCategory = Category::where('code', 'TRP')->first();

        // Kitab & Buku
        $this->createKitabItems($kitabCategory);
        
        // Alat Tulis
        $this->createAtkItems($atkCategory);
        
        // Elektronik
        $this->createElektronikItems($elektronikCategory);
        
        // Furnitur
        $this->createFurniturItems($furniturCategory);
        
        // Komputer & Aksesoris
        $this->createKomputerItems($komputerCategory);
        
        // Proyektor & Audio
        $this->createProyektorItems($proyektorCategory);
        
        // Peralatan Dapur
        $this->createDapurItems($dapurCategory);
        
        // Peralatan Kebersihan
        $this->createKebersihanItems($kebersihanCategory);
        
        // Peralatan Olahraga
        $this->createOlahragaItems($olahragaCategory);
        
        // Peralatan Kesehatan
        $this->createKesehatanItems($kesehatanCategory);
        
        // Peralatan Pertukangan
        $this->createTukangItems($tukangCategory);
        
        // Peralatan Transportasi
        $this->createTransportItems($transportCategory);
    }

    private function createKitabItems($category)
    {
        $items = [
            ['name' => 'Kitab Safinatun Najah', 'stock' => 15, 'unit' => 'eksemplar', 'purchase_price' => 25000],
            ['name' => 'Kitab Ta\'limul Muta\'allim', 'stock' => 12, 'unit' => 'eksemplar', 'purchase_price' => 30000],
            ['name' => 'Kitab Fathul Qarib', 'stock' => 10, 'unit' => 'eksemplar', 'purchase_price' => 35000],
            ['name' => 'Kitab Fathul Mu\'in', 'stock' => 8, 'unit' => 'eksemplar', 'purchase_price' => 40000],
            ['name' => 'Al-Quran Mushaf Utsmani', 'stock' => 25, 'unit' => 'eksemplar', 'purchase_price' => 150000],
            ['name' => 'Buku Hadits Arba\'in', 'stock' => 20, 'unit' => 'eksemplar', 'purchase_price' => 20000],
            ['name' => 'Buku Aqidah Ahlus Sunnah', 'stock' => 18, 'unit' => 'eksemplar', 'purchase_price' => 25000],
            ['name' => 'Buku Fiqih Ibadah', 'stock' => 15, 'unit' => 'eksemplar', 'purchase_price' => 30000],
        ];

        foreach ($items as $item) {
            $this->createItem($category, $item);
        }
    }

    private function createAtkItems($category)
    {
        $items = [
            ['name' => 'Pulpen Pilot', 'stock' => 100, 'unit' => 'pcs', 'purchase_price' => 5000],
            ['name' => 'Buku Tulis A4', 'stock' => 50, 'unit' => 'pack', 'purchase_price' => 25000],
            ['name' => 'Pensil 2B', 'stock' => 80, 'unit' => 'pcs', 'purchase_price' => 3000],
            ['name' => 'Penghapus', 'stock' => 60, 'unit' => 'pcs', 'purchase_price' => 2000],
            ['name' => 'Penggaris 30cm', 'stock' => 40, 'unit' => 'pcs', 'purchase_price' => 8000],
            ['name' => 'Spidol Papan Tulis', 'stock' => 30, 'unit' => 'pcs', 'purchase_price' => 15000],
            ['name' => 'Kertas HVS A4', 'stock' => 20, 'unit' => 'rim', 'purchase_price' => 45000],
            ['name' => 'Stapler', 'stock' => 15, 'unit' => 'pcs', 'purchase_price' => 25000],
        ];

        foreach ($items as $item) {
            $this->createItem($category, $item);
        }
    }

    private function createElektronikItems($category)
    {
        $items = [
            ['name' => 'Kipas Angin Standing', 'stock' => 8, 'unit' => 'unit', 'purchase_price' => 350000],
            ['name' => 'Lampu LED 10W', 'stock' => 25, 'unit' => 'pcs', 'purchase_price' => 25000],
            ['name' => 'Kabel Listrik 10m', 'stock' => 15, 'unit' => 'roll', 'purchase_price' => 75000],
            ['name' => 'Stop Kontak 4 Lubang', 'stock' => 12, 'unit' => 'pcs', 'purchase_price' => 35000],
            ['name' => 'Charger HP Universal', 'stock' => 20, 'unit' => 'pcs', 'purchase_price' => 15000],
            ['name' => 'Speaker Bluetooth', 'stock' => 5, 'unit' => 'unit', 'purchase_price' => 200000],
        ];

        foreach ($items as $item) {
            $this->createItem($category, $item);
        }
    }

    private function createFurniturItems($category)
    {
        $items = [
            ['name' => 'Meja Belajar', 'stock' => 20, 'unit' => 'unit', 'purchase_price' => 500000],
            ['name' => 'Kursi Plastik', 'stock' => 50, 'unit' => 'pcs', 'purchase_price' => 75000],
            ['name' => 'Lemari Buku', 'stock' => 8, 'unit' => 'unit', 'purchase_price' => 800000],
            ['name' => 'Rak Sepatu', 'stock' => 10, 'unit' => 'unit', 'purchase_price' => 200000],
            ['name' => 'Tempat Tidur', 'stock' => 15, 'unit' => 'unit', 'purchase_price' => 1200000],
            ['name' => 'Kasur Busa', 'stock' => 15, 'unit' => 'pcs', 'purchase_price' => 300000],
        ];

        foreach ($items as $item) {
            $this->createItem($category, $item);
        }
    }

    private function createKomputerItems($category)
    {
        $items = [
            ['name' => 'Laptop Asus', 'stock' => 3, 'unit' => 'unit', 'purchase_price' => 8000000],
            ['name' => 'Printer Epson L120', 'stock' => 2, 'unit' => 'unit', 'purchase_price' => 1500000],
            ['name' => 'Mouse Wireless', 'stock' => 10, 'unit' => 'pcs', 'purchase_price' => 50000],
            ['name' => 'Keyboard USB', 'stock' => 8, 'unit' => 'pcs', 'purchase_price' => 75000],
            ['name' => 'Flashdisk 16GB', 'stock' => 15, 'unit' => 'pcs', 'purchase_price' => 80000],
        ];

        foreach ($items as $item) {
            $this->createItem($category, $item);
        }
    }

    private function createProyektorItems($category)
    {
        $items = [
            ['name' => 'Proyektor Epson', 'stock' => 2, 'unit' => 'unit', 'purchase_price' => 3500000],
            ['name' => 'Layar Proyektor 2x3m', 'stock' => 2, 'unit' => 'unit', 'purchase_price' => 500000],
            ['name' => 'Speaker Active', 'stock' => 3, 'unit' => 'unit', 'purchase_price' => 400000],
            ['name' => 'Microphone Wireless', 'stock' => 2, 'unit' => 'unit', 'purchase_price' => 300000],
        ];

        foreach ($items as $item) {
            $this->createItem($category, $item);
        }
    }

    private function createDapurItems($category)
    {
        $items = [
            ['name' => 'Rice Cooker 1.8L', 'stock' => 3, 'unit' => 'unit', 'purchase_price' => 400000],
            ['name' => 'Kompor Gas 2 Tungku', 'stock' => 2, 'unit' => 'unit', 'purchase_price' => 350000],
            ['name' => 'Panci Stainless', 'stock' => 8, 'unit' => 'pcs', 'purchase_price' => 150000],
            ['name' => 'Wajan Anti Lengket', 'stock' => 5, 'unit' => 'pcs', 'purchase_price' => 200000],
            ['name' => 'Piring Melamin', 'stock' => 100, 'unit' => 'pcs', 'purchase_price' => 15000],
            ['name' => 'Gelas Plastik', 'stock' => 80, 'unit' => 'pcs', 'purchase_price' => 8000],
        ];

        foreach ($items as $item) {
            $this->createItem($category, $item);
        }
    }

    private function createKebersihanItems($category)
    {
        $items = [
            ['name' => 'Sapu Lantai', 'stock' => 15, 'unit' => 'pcs', 'purchase_price' => 25000],
            ['name' => 'Kemoceng', 'stock' => 10, 'unit' => 'pcs', 'purchase_price' => 15000],
            ['name' => 'Ember Plastik', 'stock' => 12, 'unit' => 'pcs', 'purchase_price' => 30000],
            ['name' => 'Pel Lantai', 'stock' => 8, 'unit' => 'pcs', 'purchase_price' => 40000],
            ['name' => 'Sabun Cuci Piring', 'stock' => 20, 'unit' => 'botol', 'purchase_price' => 15000],
            ['name' => 'Pembersih Lantai', 'stock' => 10, 'unit' => 'botol', 'purchase_price' => 25000],
        ];

        foreach ($items as $item) {
            $this->createItem($category, $item);
        }
    }

    private function createOlahragaItems($category)
    {
        $items = [
            ['name' => 'Bola Sepak', 'stock' => 5, 'unit' => 'pcs', 'purchase_price' => 150000],
            ['name' => 'Bola Voli', 'stock' => 3, 'unit' => 'pcs', 'purchase_price' => 120000],
            ['name' => 'Jaring Badminton', 'stock' => 2, 'unit' => 'unit', 'purchase_price' => 200000],
            ['name' => 'Raket Badminton', 'stock' => 8, 'unit' => 'pcs', 'purchase_price' => 80000],
            ['name' => 'Shuttlecock', 'stock' => 20, 'unit' => 'pack', 'purchase_price' => 25000],
        ];

        foreach ($items as $item) {
            $this->createItem($category, $item);
        }
    }

    private function createKesehatanItems($category)
    {
        $items = [
            ['name' => 'Kotak P3K', 'stock' => 3, 'unit' => 'unit', 'purchase_price' => 150000],
            ['name' => 'Perban Elastis', 'stock' => 10, 'unit' => 'roll', 'purchase_price' => 25000],
            ['name' => 'Betadine', 'stock' => 8, 'unit' => 'botol', 'purchase_price' => 30000],
            ['name' => 'Kapas', 'stock' => 15, 'unit' => 'pack', 'purchase_price' => 15000],
            ['name' => 'Termometer', 'stock' => 2, 'unit' => 'pcs', 'purchase_price' => 50000],
        ];

        foreach ($items as $item) {
            $this->createItem($category, $item);
        }
    }

    private function createTukangItems($category)
    {
        $items = [
            ['name' => 'Palu', 'stock' => 5, 'unit' => 'pcs', 'purchase_price' => 75000],
            ['name' => 'Obeng Set', 'stock' => 3, 'unit' => 'set', 'purchase_price' => 150000],
            ['name' => 'Tang Kombinasi', 'stock' => 4, 'unit' => 'pcs', 'purchase_price' => 80000],
            ['name' => 'Gergaji Kayu', 'stock' => 2, 'unit' => 'pcs', 'purchase_price' => 120000],
            ['name' => 'Meteran 5m', 'stock' => 3, 'unit' => 'pcs', 'purchase_price' => 45000],
        ];

        foreach ($items as $item) {
            $this->createItem($category, $item);
        }
    }

    private function createTransportItems($category)
    {
        $items = [
            ['name' => 'Motor Honda Beat', 'stock' => 1, 'unit' => 'unit', 'purchase_price' => 18000000],
            ['name' => 'Sepeda Onthel', 'stock' => 3, 'unit' => 'unit', 'purchase_price' => 800000],
            ['name' => 'Helm SNI', 'stock' => 5, 'unit' => 'pcs', 'purchase_price' => 150000],
            ['name' => 'Jaket Motor', 'stock' => 3, 'unit' => 'pcs', 'purchase_price' => 200000],
        ];

        foreach ($items as $item) {
            $this->createItem($category, $item);
        }
    }

    private function createItem($category, $itemData)
    {
        // Jika itemData memiliki 'location', ubah ke 'location_id' berdasarkan nama lokasi
        if (isset($itemData['location'])) {
            $itemData['location_id'] = \App\Models\Location::where('name', $itemData['location'])->value('id');
            unset($itemData['location']);
        }
        $itemData['category_id'] = $category->id;
        Item::create($itemData);
    }

    private function generateItemCode($category, $itemName): string
    {
        $categoryCode = $category->code;
        $dateCode = Carbon::now()->format('ym');
        $codePrefix = "{$categoryCode}/{$dateCode}/";

        $latestItem = Item::where('code', 'like', $codePrefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        $sequence = 1;
        if ($latestItem) {
            $lastSequence = (int) substr($latestItem->code, -3);
            $sequence = $lastSequence + 1;
        }
        
        return $codePrefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    private function getLocationByCategory($categoryCode): string
    {
        $locations = [
            'KTB' => 'Perpustakaan',
            'ATK' => 'Gudang ATK',
            'ELK' => 'Gudang Elektronik',
            'FRN' => 'Gudang Furnitur',
            'KMP' => 'Ruang Komputer',
            'PRJ' => 'Aula',
            'DKP' => 'Dapur',
            'KBR' => 'Gudang Kebersihan',
            'OLG' => 'Lapangan Olahraga',
            'KSH' => 'UKS',
            'TKN' => 'Bengkel',
            'TRP' => 'Garasi',
        ];

        return $locations[$categoryCode] ?? 'Gudang Utama';
    }
}