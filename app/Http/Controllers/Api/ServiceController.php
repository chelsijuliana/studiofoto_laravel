<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\ServicePackage;

class ServiceController extends Controller
{
    // GET /api/services
    // Dipakai di halaman Dashboard, nampilin 6 kategori + harga termurah
    public function index()
    {
        $categories = ServiceCategory::withMin('packages', 'harga')->get();

        $data = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'nama_kategori' => $category->nama_kategori,
                'kuota_harian' => $category->kuota_harian,
                'harga_mulai' => $category->packages_min_harga,
            ];
        });

        return response()->json($data);
    }

    // GET /api/services/{id}
    // Dipakai di halaman List Paket, nampilin semua paket dalam 1 kategori
    public function show(ServiceCategory $service)
    {
        $service->load('packages');

        return response()->json([
            'id' => $service->id,
            'nama_kategori' => $service->nama_kategori,
            'kuota_harian' => $service->kuota_harian,
            'butuh_ruangan' => $service->butuh_ruangan,
            'packages' => $service->packages->map(function ($package) {
                return [
                    'id' => $package->id,
                    'nama_paket' => $package->nama_paket,
                    'harga' => $package->harga,
                    'cakupan_acara' => $package->cakupan_acara,
                    'durasi_menit' => $package->durasi_menit,
                    'max_orang' => $package->max_orang,
                ];
            }),
        ]);
    }

    // GET /api/packages/{id}
    // Dipakai di halaman Detail Paket, nampilin fasilitas lengkap + add-on kategori
    public function packageDetail(ServicePackage $package)
    {
        $package->load(['facilities', 'category', 'category.addons']);

        return response()->json([
            'id' => $package->id,
            'nama_paket' => $package->nama_paket,
            'harga' => $package->harga,
            'durasi_menit' => $package->durasi_menit,
            'max_orang' => $package->max_orang,
            'cakupan_acara' => $package->cakupan_acara,
            'atribut_lain' => $package->atribut_lain,
            'kategori' => [
                'id' => $package->category->id,
                'nama_kategori' => $package->category->nama_kategori,
                'butuh_ruangan' => $package->category->butuh_ruangan,
                'dp_type' => $package->category->dp_type,
                'dp_value' => $package->category->dp_value,
            ],
            'fasilitas' => $package->facilities->map(fn ($f) => [
                'item_name' => $f->item_name,
                'qty' => $f->qty,
            ]),
            'addons' => $package->category->addons->map(fn ($a) => [
                'id' => $a->id,
                'nama_addon' => $a->nama_addon,
                'harga' => $a->harga,
                'tipe' => $a->tipe,
            ]),
        ]);
    }
}