<?php

namespace App\Console\Commands;

use App\Models\BookingSession;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class KirimReminderH3Jam extends Command
{
    protected $signature = 'reminder:h3jam';
    protected $description = 'Kirim reminder 3 jam sebelum jadwal (dijalankan tiap 30 menit)';

    public function handle(NotificationService $notificationService): void
    {
        $targetWaktu = now()->addHours(3);
        $toleransiMenit = 15; // biar gak kelewat kalau command telat jalan beberapa menit

        $sessions = BookingSession::whereDate('tanggal', $targetWaktu->toDateString())
            ->whereHas('booking', fn ($q) => $q->where('status', 'confirmed'))
            ->with('booking.user', 'booking.package')
            ->get()
            ->filter(function ($session) use ($targetWaktu, $toleransiMenit) {
                $jamSesi = Carbon::parse($session->tanggal->toDateString().' '.$session->jam_mulai);

                return abs($jamSesi->diffInMinutes($targetWaktu, false)) <= $toleransiMenit;
            });

        foreach ($sessions as $session) {
            $booking = $session->booking;

            $notificationService->kirim(
                $booking->user,
                'reminder_h3jam',
                'Jadwal 3 jam lagi',
                "Jadwal {$booking->package->nama_paket} kamu jam {$session->jam_mulai} hari ini, siap-siap ya.",
                $booking->id
            );
        }

        $this->info("Reminder H-3 jam terkirim untuk {$sessions->count()} sesi.");
    }
}