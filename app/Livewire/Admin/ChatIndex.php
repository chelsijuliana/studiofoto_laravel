<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use Livewire\Component;

class ChatIndex extends Component
{
    public string $filter = 'semua';

    public function setFilter(string $filter)
    {
        $this->filter = $filter;
    }

    public function getStatusLabelsProperty(): array
    {
        return [
            'semua'                  => 'Semua',
            'menunggu_konfirmasi'    => 'Menunggu Konfirmasi',
            'menunggu_pembayaran_dp' => 'Menunggu Pembayaran DP',
            'confirmed'              => 'Dikonfirmasi',
            'sedang_diproses'        => 'Sedang Diproses',
            'hasil_siap'             => 'Hasil Siap',
            'selesai'                => 'Selesai',
            'dibatalkan'             => 'Dibatalkan',
        ];
    }

    public function render()
    {
        $query = Booking::with('user', 'package.category', 'chatMessages')
            ->whereHas('chatMessages')
            ->get()
            ->map(function ($booking) {
                $pesanTerakhir = $booking->chatMessages->sortByDesc('created_at')->first();
                $belumDibalas = $booking->chatMessages
                    ->where('sender_type', 'customer')
                    ->where('is_read', false)
                    ->count();

                return (object) [
                    'booking' => $booking,
                    'pesan_terakhir' => $pesanTerakhir,
                    'belum_dibalas' => $belumDibalas,
                ];
            })
            ->sortByDesc(fn ($item) => $item->pesan_terakhir->created_at)
            ->values();

        if ($this->filter === 'belum_dibalas') {
            $query = $query->filter(fn ($item) => $item->belum_dibalas > 0)->values();
        }

        return view('livewire.admin.chat-index', [
            'threads' => $query,
        ]);
    }
}