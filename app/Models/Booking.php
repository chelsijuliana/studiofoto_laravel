<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 'package_id', 'status', 'total_harga',
        'catatan', 'status_pengambilan_fisik', 'tanggal_diambil',
    ];

    protected $casts = [
        'status_pengambilan_fisik' => 'boolean',
        'tanggal_diambil' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(ServicePackage::class, 'package_id');
    }

    public function sessions()
    {
        return $this->hasMany(BookingSession::class);
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function files()
    {
        return $this->hasMany(BookingFile::class);
    }

    public function items()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function totalDibayar(): float
    {
        return $this->payments()->where('status', 'success')->sum('jumlah');
    }

    public function sisaPelunasan(): float
    {
        return $this->total_harga - $this->totalDibayar();
    }
}