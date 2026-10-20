<?php

namespace Database\Seeders;

use App\Models\ServicePackage;
use Illuminate\Database\Seeder;

class UpdatePackageDataSeeder extends Seeder
{
    public function run(): void
    {
        // ==================== WEDDING PACK ====================
        $this->updatePaket('Wedding Pack', 'Basic', [
            ['Album Exclusive', 1],
            ['Cetak foto 4R', 100],
            ['Cetak foto 10R+', 3],
            ['Cetak 20R+ pakai frame', 1],
            ['Flashdisk 16GB (all file)', 0], // 0 = tidak tampilkan qty
        ]);

        $this->updatePaket('Wedding Pack', 'Silver', [
            ['Album Exclusive', 1],
            ['Cetak foto 4R', 150],
            ['Cetak foto 10R+', 4],
            ['Cetak 20R+ pakai frame', 1],
            ['Flashdisk 16GB (all file)', 0],
        ]);

        $this->updatePaket('Wedding Pack', 'Gold', [
            ['Album Exclusive', 2],
            ['Cetak foto 4R', 250],
            ['Cetak foto 10R+', 6],
            ['Cetak 20R+ pakai frame', 2],
            ['Flashdisk 32GB (all file)', 0],
        ]);

        $this->updatePaket('Wedding Pack', 'Platinum', [
            ['Wedding Book 10 Sheet + Box', 1],
            ['Album Exclusive', 3],
            ['Cetak foto 4R', 300],
            ['Cetak foto 10R+', 9],
            ['Cetak 20R+ pakai frame', 3],
            ['Flashdisk 64GB (all file)', 0],
        ]);

        // ==================== FAMILY PACKAGE ====================
        $this->updatePaket('Family Package', 'Silver', [
            ['Cetak Foto 16R+', 1],
            ['Bingkai 16R+', 1],
            ['Cetak Foto 10R+', 1],
            ['Free Soft Copy', 0],
        ], [
            'jumlah_sesi' => 2,
            'jenis_sesi' => ['Sesi Foto Keluarga', 'Sesi Foto Anak'],
            'jepretan_per_sesi' => '10x jepret',
            'durasi_per_sesi' => 'Maks. 10 menit',
        ]);

        $this->updatePaket('Family Package', 'Gold', [
            ['Cetak Foto 20R+', 1],
            ['Bingkai 20R+', 1],
            ['Cetak Foto 10R+', 1],
            ['Bingkai Foto 10R+', 1],
            ['Free Soft Copy', 0],
        ], [
            'jumlah_sesi' => 3,
            'jenis_sesi' => ['Sesi Foto Keluarga', 'Sesi Foto Ayah & Ibu', 'Sesi Foto Anak'],
            'jepretan_per_sesi' => '10x jepret',
            'durasi_per_sesi' => 'Maks. 10 menit',
        ]);

        $this->updatePaket('Family Package', 'Platinum', [
            ['Cetak Foto 36R+', 1],
            ['Bingkai 36R+', 1],
            ['Cetak Foto 20R+', 1],
            ['Bingkai 20R+', 1],
            ['Cetak Foto 10R+', 2],
            ['Free Soft Copy', 0],
        ], [
            'jumlah_sesi' => 4,
            'jenis_sesi' => ['Sesi Foto Keluarga', 'Sesi Foto Keluarga', 'Sesi Foto Ayah & Ibu', 'Sesi Foto Anak'],
            'jepretan_per_sesi' => '10x jepret',
            'durasi_per_sesi' => 'Maks. 10 menit',
        ]);

        // ==================== GRADUATION PACKAGE ====================
        $this->updatePaket('Graduation Package', 'Silver', [
            ['Cetak Foto 16R+', 1],
            ['Cetak Foto 10R+', 3],
            ['Bingkai 10R+', 1],
            ['Free Soft Copy', 0],
        ], [
            'jumlah_sesi' => 4,
            'jenis_sesi' => ['Sesi Toga', 'Sesi Kebaya/Jas', 'Sesi Bersama Ayah & Ibu', 'Sesi Keluarga Inti'],
            'jepretan_per_sesi' => '7-10x jepret',
            'durasi_per_sesi' => 'Maks. 5 menit',
        ]);

        $this->updatePaket('Graduation Package', 'Gold', [
            ['Cetak Foto 20R+', 1],
            ['Cetak Foto 16R+', 1],
            ['Bingkai 16R+', 1],
            ['Cetak Foto 10R+', 3],
            ['Free Soft Copy', 0],
        ], [
            'jumlah_sesi' => 5,
            'jenis_sesi' => ['Sesi Toga', 'Sesi Kebaya/Jas', 'Sesi Bersama Ayah & Ibu', 'Sesi Keluarga Inti', 'Sesi Keluarga Besar'],
            'jepretan_per_sesi' => '7-10x jepret',
            'durasi_per_sesi' => 'Maks. 5 menit',
        ]);

        $this->updatePaket('Graduation Package', 'Platinum', [
            ['Cetak Foto 20R+', 1],
            ['Bingkai 20R+', 1],
            ['Cetak Foto 16R+', 1],
            ['Bingkai 16R+', 1],
            ['Cetak Foto 10R+', 4],
            ['Free Soft Copy', 0],
        ], [
            'jumlah_sesi' => 6,
            'jenis_sesi' => ['Sesi Toga', 'Sesi Kebaya/Jas', 'Sesi Bersama Ayah & Ibu', 'Sesi Keluarga Inti', '2 Sesi Keluarga Besar'],
            'jepretan_per_sesi' => '7-10x jepret',
            'durasi_per_sesi' => 'Maks. 5 menit',
        ]);

        // ==================== MATERNITY PACK ====================
        $this->updatePaket('Maternity Pack', 'Silver', [
            ['Cetak Foto 16R+', 1],
            ['Bingkai 16R+', 1],
            ['File resolusi tinggi (all file)', 0],
        ]);

        $this->updatePaket('Maternity Pack', 'Gold', [
            ['Cetak Foto 16R+', 1],
            ['Bingkai 16R+', 1],
            ['Cetak Foto 10R+', 1],
            ['Bingkai 10R+', 1],
            ['File resolusi tinggi (all file)', 0],
        ]);

        // ==================== PRE WEDDING PACKAGE ====================
        $this->updatePaket('Pre Wedding Package', 'Indoor', [
            ['Cetak Foto 20R+ dengan bingkai', 1],
        ]);

        $this->updatePaket('Pre Wedding Package', 'Outdoor', [
            ['Cetak Foto 20R+ dengan bingkai', 1],
        ]);

        $this->command->info('Update data paket selesai!');
    }

    private function updatePaket(string $namaKategori, string $namaPaket, array $fasilitas, array $atributTambahan = []): void
    {
        $package = ServicePackage::whereHas('category', fn ($q) => $q->where('nama_kategori', $namaKategori))
            ->where('nama_paket', $namaPaket)
            ->first();

        if (! $package) {
            $this->command->warn("Paket tidak ditemukan: $namaKategori - $namaPaket");
            return;
        }

        // Hapus fasilitas lama, ganti dengan data baru
        $package->facilities()->delete();
        foreach ($fasilitas as [$namaItem, $qty]) {
            $package->facilities()->create(['item_name' => $namaItem, 'qty' => $qty]);
        }

        // Gabungkan atribut_lain lama (kalau ada) dengan yang baru, jangan hilangkan yang sudah ada
        if (! empty($atributTambahan)) {
            $atributLama = $package->atribut_lain ?? [];
            $package->update(['atribut_lain' => array_merge($atributLama, $atributTambahan)]);
        }

        $this->command->info("Updated: $namaKategori - $namaPaket");
    }
}