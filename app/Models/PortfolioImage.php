<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioImage extends Model
{
    protected $fillable = ['image_url', 'kategori', 'uploaded_at'];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];
}