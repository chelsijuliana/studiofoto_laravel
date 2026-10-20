<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSession extends Model
{
    protected $fillable = [
        'booking_id', 'jenis_sesi', 'tanggal', 'jam_mulai', 'jam_selesai',
        'lokasi_lat', 'lokasi_lng', 'alamat', 'catatan', 'perlu_luar_kota',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'perlu_luar_kota' => 'boolean',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}