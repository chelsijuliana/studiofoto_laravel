<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use App\Models\ServicePackage;
use Illuminate\Database\Seeder;

class ServicePackageSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedWedding();
        $this->seedFamily();
        $this->seedGraduation();
        $this->seedMaternity();
        $this->seedPreWedding();
        $this->seedPassFoto();
    }

    private function seedWedding(): void
    {
        $categoryId = ServiceCategory::where('nama_kategori', 'Wedding Pack')->value('id');

        $packages = [
            [
                'nama_paket' => 'Basic',
                'harga' => 2500000,
                'cakupan_acara' => 'pesta_saja',
                'facilities' => [
                    ['item_name' => 'Album Exclusive', 'qty' => 1],
                    ['item_name' => 'Cetak foto 4R', 'qty' => 100],
                    ['item_name' => 'Cetak foto 10R+', 'qty' => 3],
                    ['item_name' => 'Cetak 20R+ pakai frame', 'qty' => 1],
                    ['item_name' => 'Flashdisk 16GB (all file)', 'qty' => 1],
                ],
            ],
            [
                'nama_paket' => 'Silver',
                'harga' => 3500000,
                'cakupan_acara' => 'akad_pesta',
                'facilities' => [
                    ['item_name' => 'Album Exclusive', 'qty' => 1],
                    ['item_name' => 'Cetak foto 4R', 'qty' => 150],
                    ['item_name' => 'Cetak foto 10R+', 'qty' => 4],
                    ['item_name' => 'Cetak 20R+ pakai frame', 'qty' => 1],
                    ['item_name' => 'Flashdisk 16GB (all file)', 'qty' => 1],
                ],
            ],
            [
                'nama_paket' => 'Gold',
                'harga' => 5000000,
                'cakupan_acara' => 'akad_pesta',
                'facilities' => [
                    ['item_name' => 'Album Exclusive', 'qty' => 2],
                    ['item_name' => 'Cetak foto 4R', 'qty' => 250],
                    ['item_name' => 'Cetak foto 10R+', 'qty' => 6],
                    ['item_name' => 'Cetak 20R+ pakai frame', 'qty' => 2],
                    ['item_name' => 'Flashdisk 32GB (all file)', 'qty' => 1],
                ],
            ],
            [
                'nama_paket' => 'Platinum',
                'harga' => 8000000,
                'cakupan_acara' => 'akad_pesta',
                'facilities' => [
                    ['item_name' => 'Wedding Book 10 Sheet + Box', 'qty' => 1],
                    ['item_name' => 'Album Exclusive', 'qty' => 3],
                    ['item_name' => 'Cetak foto 4R', 'qty' => 300],
                    ['item_name' => 'Cetak foto 10R+', 'qty' => 9],
                    ['item_name' => 'Cetak 20R+ pakai frame', 'qty' => 3],
                    ['item_name' => 'Flashdisk 64GB (all file)', 'qty' => 1],
                ],
            ],
        ];

        $this->createPackages($categoryId, $packages);
    }

    private function seedFamily(): void
    {
        $categoryId = ServiceCategory::where('nama_kategori', 'Family Package')->value('id');

        $packages = [
            [
                'nama_paket' => 'Silver',
                'harga' => 350000,
                'durasi_menit' => 20,
                'max_orang' => 6,
                'atribut_lain' => ['pilihan_latar' => 2, 'jumlah_sesi' => 2],
                'facilities' => [
                    ['item_name' => 'Cetak foto 16R+', 'qty' => 1],
                    ['item_name' => 'Bingkai 16R+', 'qty' => 1],
                    ['item_name' => 'Cetak foto 10R+', 'qty' => 1],
                    ['item_name' => 'Free softcopy', 'qty' => 1],
                ],
            ],
            [
                'nama_paket' => 'Gold',
                'harga' => 600000,
                'durasi_menit' => 30,
                'max_orang' => 10,
                'atribut_lain' => ['pilihan_latar' => 2, 'jumlah_sesi' => 3],
                'facilities' => [
                    ['item_name' => 'Cetak foto 20R+', 'qty' => 1],
                    ['item_name' => 'Bingkai 20R+', 'qty' => 1],
                    ['item_name' => 'Cetak foto 10R+', 'qty' => 1],
                    ['item_name' => 'Bingkai foto 10R+', 'qty' => 1],
                    ['item_name' => 'Free softcopy', 'qty' => 1],
                ],
            ],
            [
                'nama_paket' => 'Platinum',
                'harga' => 1200000,
                'durasi_menit' => 40,
                'max_orang' => 15,
                'atribut_lain' => ['pilihan_latar' => 3, 'jumlah_sesi' => 4],
                'facilities' => [
                    ['item_name' => 'Cetak foto 36R+', 'qty' => 1],
                    ['item_name' => 'Bingkai 36R+', 'qty' => 1],
                    ['item_name' => 'Cetak foto 20R+', 'qty' => 1],
                    ['item_name' => 'Bingkai foto 20R+', 'qty' => 1],
                    ['item_name' => 'Cetak foto 10R+', 'qty' => 2],
                    ['item_name' => 'Free softcopy', 'qty' => 1],
                ],
            ],
        ];

        $this->createPackages($categoryId, $packages);
    }

    private function seedGraduation(): void
    {
        $categoryId = ServiceCategory::where('nama_kategori', 'Graduation Package')->value('id');

        $packages = [
            [
                'nama_paket' => 'Silver',
                'harga' => 300000,
                'durasi_menit' => 20,
                'max_orang' => 10,
                'atribut_lain' => ['pilihan_latar' => 2, 'jumlah_sesi' => 4],
                'facilities' => [
                    ['item_name' => 'Cetak foto 16R+', 'qty' => 1],
                    ['item_name' => 'Cetak foto 10R+', 'qty' => 3],
                    ['item_name' => 'Bingkai 10R+', 'qty' => 1],
                    ['item_name' => 'Free softcopy', 'qty' => 1],
                ],
            ],
            [
                'nama_paket' => 'Gold',
                'harga' => 500000,
                'durasi_menit' => 25,
                'max_orang' => 15,
                'atribut_lain' => ['pilihan_latar' => 3, 'jumlah_sesi' => 5],
                'facilities' => [
                    ['item_name' => 'Cetak foto 20R+', 'qty' => 1],
                    ['item_name' => 'Cetak foto 16R+', 'qty' => 1],
                    ['item_name' => 'Bingkai 16R+', 'qty' => 1],
                    ['item_name' => 'Cetak foto 10R+', 'qty' => 3],
                    ['item_name' => 'Free softcopy', 'qty' => 1],
                ],
            ],
            [
                'nama_paket' => 'Platinum',
                'harga' => 850000,
                'durasi_menit' => 30,
                'max_orang' => 20,
                'atribut_lain' => ['pilihan_latar' => 3, 'jumlah_sesi' => 6],
                'facilities' => [
                    ['item_name' => 'Cetak foto 20R+', 'qty' => 1],
                    ['item_name' => 'Bingkai 20R+', 'qty' => 1],
                    ['item_name' => 'Cetak foto 16R+', 'qty' => 1],
                    ['item_name' => 'Bingkai 16R+', 'qty' => 1],
                    ['item_name' => 'Cetak foto 10R+', 'qty' => 4],
                    ['item_name' => 'Free softcopy', 'qty' => 1],
                ],
            ],
        ];

        $this->createPackages($categoryId, $packages);
    }

    private function seedMaternity(): void
    {
        $categoryId = ServiceCategory::where('nama_kategori', 'Maternity Pack')->value('id');

        $packages = [
            [
                'nama_paket' => 'Silver',
                'harga' => 400000,
                'durasi_menit' => 30,
                'atribut_lain' => ['pilihan_background' => 2, 'jumlah_pose' => 5, 'jumlah_file_edit' => 5],
                'facilities' => [
                    ['item_name' => 'Cetak foto 16R+', 'qty' => 1],
                    ['item_name' => 'Frame 16R+', 'qty' => 1],
                    ['item_name' => 'All file resolusi tinggi', 'qty' => 1],
                ],
            ],
            [
                'nama_paket' => 'Gold',
                'harga' => 750000,
                'durasi_menit' => 60,
                'atribut_lain' => ['pilihan_background' => 3, 'jumlah_pose' => 8, 'jumlah_file_edit' => 8],
                'facilities' => [
                    ['item_name' => 'Cetak foto 16R+', 'qty' => 1],
                    ['item_name' => 'Frame 16R+', 'qty' => 1],
                    ['item_name' => 'Cetak foto 10R+', 'qty' => 1],
                    ['item_name' => 'Frame 10R+', 'qty' => 1],
                    ['item_name' => 'All file resolusi tinggi', 'qty' => 1],
                ],
            ],
        ];

        $this->createPackages($categoryId, $packages);
    }

    private function seedPreWedding(): void
    {
        $categoryId = ServiceCategory::where('nama_kategori', 'Pre Wedding Package')->value('id');

        $packages = [
            [
                'nama_paket' => 'Indoor',
                'harga' => 1000000,
                'durasi_menit' => 60,
                'atribut_lain' => ['tipe' => 'indoor', 'butuh_ruangan' => true],
                'facilities' => [
                    ['item_name' => 'Cetak 20R+ dengan bingkai', 'qty' => 1],
                ],
            ],
            [
                'nama_paket' => 'Outdoor',
                'harga' => 1500000,
                'durasi_menit' => 120,
                'atribut_lain' => ['tipe' => 'outdoor', 'butuh_ruangan' => false],
                'facilities' => [
                    ['item_name' => 'Cetak 20R+ dengan bingkai', 'qty' => 1],
                ],
            ],
        ];

        $this->createPackages($categoryId, $packages);
    }

    private function seedPassFoto(): void
    {
        $categoryId = ServiceCategory::where('nama_kategori', 'Pass Foto')->value('id');

        $packages = [
            [
                'nama_paket' => 'Biasa',
                'harga' => 35000,
                'facilities' => [
                    ['item_name' => 'Cetak 3x4', 'qty' => 5],
                ],
            ],
            [
                'nama_paket' => 'Plus Jas/Blazer',
                'harga' => 45000,
                'facilities' => [
                    ['item_name' => 'Cetak 3x4', 'qty' => 5],
                ],
            ],
        ];

        $this->createPackages($categoryId, $packages);
    }

    private function createPackages(int $categoryId, array $packages): void
    {
        foreach ($packages as $data) {
            $facilities = $data['facilities'];
            unset($data['facilities']);

            $package = ServicePackage::create(array_merge($data, [
                'category_id' => $categoryId,
            ]));

            foreach ($facilities as $facility) {
                $package->facilities()->create($facility);
            }
        }
    }
}