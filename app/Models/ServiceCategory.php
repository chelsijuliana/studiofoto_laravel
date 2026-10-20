<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    protected $fillable = [
        'nama_kategori', 'kuota_harian', 'butuh_ruangan', 'dp_type', 'dp_value',
    ];

    protected $casts = [
        'butuh_ruangan' => 'boolean',
    ];

    public function packages()
    {
        return $this->hasMany(ServicePackage::class, 'category_id');
    }

    public function addons()
    {
        return $this->hasMany(PackageAddon::class, 'category_id');
    }

    public function hitungDp(float $totalHarga): float
    {
        return match ($this->dp_type) {
            'percentage' => $totalHarga * ($this->dp_value / 100),
            'flat' => $this->dp_value,
            default => 0,
        };
    }
}