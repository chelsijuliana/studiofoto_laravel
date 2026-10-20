<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\BookingSession;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class KirimReminderHasilBelumUpload extends Command
{
    protected $signature = 'reminder:hasil-belum-upload';
    protected $description = 'Ingatkan admin kalau sesi sudah lewat 2 hari tapi hasil belum diupload';

    public function handle(NotificationService $notificationService): void
    {
        $duaHariLalu = now()->subDays(2)->toDateString();

        $bookings = Booking::where('status', 'confirmed')
            ->whereHas('sessions', fn ($q) => $q->whereDate('tanggal', '<=', $duaHariLalu))
            ->with('user', 'package')
            ->get();

        foreach ($bookings as $booking) {
            $notificationService->kirimKeAdmin(
                'reminder_hasil_belum_upload',
                'Hasil belum diupload',
                "Booking {$booking->user->nama} - {$booking->package->nama_paket} sudah lewat 2 hari, hasil belum diupload.",
                $booking->id
            );
        }

        $this->info("Reminder hasil belum upload terkirim untuk {$bookings->count()} booking.");
    }
}