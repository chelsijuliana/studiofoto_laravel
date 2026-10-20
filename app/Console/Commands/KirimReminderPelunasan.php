<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class KirimReminderPelunasan extends Command
{
    protected $signature = 'reminder:pelunasan';
    protected $description = 'Ingatkan pelanggan yang sisa pelunasannya belum lunas dan jadwal sudah dekat';

    public function handle(NotificationService $notificationService): void
    {
        $besok = now()->addDay()->toDateString();

        $bookings = Booking::where('status', 'confirmed')
            ->whereHas('sessions', fn ($q) => $q->whereDate('tanggal', $besok))
            ->with('user', 'package')
            ->get();

        $terkirim = 0;

        foreach ($bookings as $booking) {
            if ($booking->sisaPelunasan() > 0) {
                $notificationService->kirim(
                    $booking->user,
                    'reminder_pelunasan',
                    'Sisa pelunasan belum dibayar',
                    "Jadwal {$booking->package->nama_paket} kamu besok, sisa pelunasan Rp".number_format($booking->sisaPelunasan(), 0, ',', '.')." belum dibayar.",
                    $booking->id
                );

                $terkirim++;
            }
        }

        $this->info("Reminder pelunasan terkirim untuk {$terkirim} booking.");
    }
}