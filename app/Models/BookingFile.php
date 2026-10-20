<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingFile extends Model
{
    protected $fillable = ['booking_id', 'file_url', 'file_type'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}