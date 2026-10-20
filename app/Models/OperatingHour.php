<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatingHour extends Model
{
    protected $table = 'operating_hours';

    protected $fillable = ['hari', 'jam_buka', 'jam_tutup', 'is_tutup'];

    protected $casts = [
        'is_tutup' => 'boolean',
    ];
}