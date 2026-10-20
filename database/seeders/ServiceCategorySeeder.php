<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'nama_kategori' => 'Wedding Pack',
                'kuota_harian' => 2,
                'butuh_ruangan' => false,
                'dp_type' => 'percentage',
                'dp_value' => 50,
            ],
            [
                'nama_kategori' => 'Family Package',
                'kuota_harian' => 5,
                'butuh_ruangan' => true,
                'dp_type' => 'flat',
                'dp_value' => 100000,
            ],
            [
                'nama_kategori' => 'Graduation Package',
                'kuota_harian' => 20,
                'butuh_ruangan' => true,
                'dp_type' => 'flat',
                'dp_value' => 100000,
            ],
            [
                'nama_kategori' => 'Maternity Pack',
                'kuota_harian' => 3,
                'butuh_ruangan' => true,
                'dp_type' => 'flat',
                'dp_value' => 100000,
            ],
            [
                // Catatan: kategori ini punya 2 paket dengan kebutuhan ruangan
                // yang beda (Indoor = true, Outdoor = false).
                // Override per paket disimpan di atribut_lain->butuh_ruangan.
                'nama_kategori' => 'Pre Wedding Package',
                'kuota_harian' => 2,
                'butuh_ruangan' => true,
                'dp_type' => 'percentage',
                'dp_value' => 50,
            ],
            [
                'nama_kategori' => 'Pass Foto',
                'kuota_harian' => null,
                'butuh_ruangan' => true,
                'dp_type' => 'none',
                'dp_value' => 0,
            ],
        ];

        foreach ($categories as $category) {
            ServiceCategory::create($category);
        }
    }
}