<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id', 'jenis', 'jumlah', 'metode_pembayaran',
        'gateway_transaction_id', 'status', 'paid_at',
        'bukti_transfer', 'catatan_admin',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}