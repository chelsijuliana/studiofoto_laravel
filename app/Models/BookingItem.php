<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    protected $fillable = ['booking_id', 'keterangan', 'qty', 'harga_satuan', 'subtotal'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}