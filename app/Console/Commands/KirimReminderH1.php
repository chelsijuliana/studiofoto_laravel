<?php

namespace App\Console\Commands;

use App\Models\BookingSession;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class KirimReminderH1 extends Command
{
    protected $signature = 'reminder:h1';
    protected $description = 'Kirim reminder H-1 untuk semua sesi booking besok';

    public function handle(NotificationService $notificationService): void
    {
        $besok = now()->addDay()->toDateString();

        $sessions = BookingSession::whereDate('tanggal', $besok)
            ->whereHas('booking', fn ($q) => $q->where('status', 'confirmed'))
            ->with('booking.user', 'booking.package')
            ->get();

        foreach ($sessions as $session) {
            $booking = $session->booking;

            $notificationService->kirim(
                $booking->user,
                'reminder_h1',
                'Jadwal besok',
                "Jadwal {$booking->package->nama_paket} kamu besok jam {$session->jam_mulai}, jangan sampai lupa ya.",
                $booking->id
            );

            $notificationService->kirimKeAdmin(
                'reminder_h1_admin',
                'Jadwal besok',
                "{$booking->user->nama} - {$booking->package->nama_paket} besok jam {$session->jam_mulai}.",
                $booking->id
            );
        }

        $this->info("Reminder H-1 terkirim untuk {$sessions->count()} sesi.");
    }
}