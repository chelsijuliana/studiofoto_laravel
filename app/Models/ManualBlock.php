<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualBlock extends Model
{
    protected $fillable = ['tanggal', 'alasan', 'admin_id'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}