<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Ruang Kelas 1A',
                'description' => 'Ruang kelas untuk santri tingkat pertama kelas A'
            ],
            [
                'name' => 'Ruang Kelas 1B',
                'description' => 'Ruang kelas untuk santri tingkat pertama kelas B'
            ],
            [
                'name' => 'Ruang Kelas 2A',
                'description' => 'Ruang kelas untuk santri tingkat kedua kelas A'
            ],
            [
                'name' => 'Ruang Kelas 2B',
                'description' => 'Ruang kelas untuk santri tingkat kedua kelas B'
            ],
            [
                'name' => 'Ruang Kelas 3A',
                'description' => 'Ruang kelas untuk santri tingkat ketiga kelas A'
            ],
            [
                'name' => 'Ruang Kelas 3B',
                'description' => 'Ruang kelas untuk santri tingkat ketiga kelas B'
            ],
            [
                'name' => 'Perpustakaan',
                'description' => 'Ruang perpustakaan untuk membaca dan meminjam buku'
            ],
            [
                'name' => 'Masjid',
                'description' => 'Tempat ibadah dan kegiatan keagamaan'
            ],
            [
                'name' => 'Kantor Pengasuh',
                'description' => 'Kantor untuk pengasuh pondok pesantren'
            ],
            [
                'name' => 'Kantor Administrasi',
                'description' => 'Kantor untuk administrasi dan keuangan'
            ],
            [
                'name' => 'Dapur Umum',
                'description' => 'Dapur untuk memasak makanan santri'
            ],
            [
                'name' => 'Gudang',
                'description' => 'Gudang penyimpanan barang dan peralatan'
            ],
            [
                'name' => 'Asrama Putra',
                'description' => 'Asrama untuk santri putra'
            ],
            [
                'name' => 'Asrama Putri',
                'description' => 'Asrama untuk santri putri'
            ],
            [
                'name' => 'Ruang Tamu',
                'description' => 'Ruang untuk menerima tamu'
            ],
            [
                'name' => 'Ruang Rapat',
                'description' => 'Ruang untuk rapat dan pertemuan'
            ],
            [
                'name' => 'Lapangan Olahraga',
                'description' => 'Lapangan untuk kegiatan olahraga'
            ],
            [
                'name' => 'Klinik Kesehatan',
                'description' => 'Klinik untuk layanan kesehatan santri'
            ],
            [
                'name' => 'Ruang Komputer',
                'description' => 'Ruang laboratorium komputer'
            ],
            [
                'name' => 'Ruang Audio Visual',
                'description' => 'Ruang untuk presentasi dan multimedia'
            ]
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
