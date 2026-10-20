<?php

namespace Database\Seeders;

use App\Models\PackageAddon;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class PackageAddonSeeder extends Seeder
{
    public function run(): void
    {
        $weddingId = ServiceCategory::where('nama_kategori', 'Wedding Pack')->value('id');
        $familyId = ServiceCategory::where('nama_kategori', 'Family Package')->value('id');
        $graduationId = ServiceCategory::where('nama_kategori', 'Graduation Package')->value('id');

        $addons = [
            [
                'category_id' => $weddingId,
                'nama_addon' => 'Video Dokumentasi 2 Jam',
                'harga' => 3000000,
                'tipe' => 'flat',
            ],
            [
                'category_id' => $weddingId,
                'nama_addon' => 'Transport & Akomodasi Luar Kota',
                'harga' => 0,
                'tipe' => 'nego',
            ],
            [
                'category_id' => $familyId,
                'nama_addon' => 'Tambahan Sesi',
                'harga' => 50000,
                'tipe' => 'per_sesi',
            ],
            [
                'category_id' => $graduationId,
                'nama_addon' => 'Tambahan Sesi',
                'harga' => 50000,
                'tipe' => 'per_sesi',
            ],
            [
                'category_id' => $graduationId,
                'nama_addon' => 'Tambahan Orang',
                'harga' => 10000,
                'tipe' => 'per_orang',
            ],
        ];

        foreach ($addons as $addon) {
            PackageAddon::create($addon);
        }
    }
}