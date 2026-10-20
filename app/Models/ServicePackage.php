<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePackage extends Model
{
    protected $fillable = [
        'category_id', 'nama_paket', 'harga', 'durasi_menit',
        'max_orang', 'cakupan_acara', 'atribut_lain',
    ];

    protected $casts = [
        'atribut_lain' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function facilities()
    {
        return $this->hasMany(PackageFacility::class, 'package_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'package_id');
    }
}