<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageAddon extends Model
{
    protected $fillable = ['category_id', 'nama_addon', 'harga', 'tipe'];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }
}