<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class KirimReminderPengambilan extends Command
{
    protected $signature = 'reminder:pengambilan';
    protected $description = 'Kirim reminder untuk cetak fisik yang belum diambil (H+3, H+7, H+14)';

    public function handle(NotificationService $notificationService): void
    {
        $bookings = Booking::whereIn('status', ['hasil_siap'])
            ->where('status_pengambilan_fisik', false)
            ->with('user', 'package')
            ->get();

        $terkirim = 0;

        foreach ($bookings as $booking) {
            $hariSejakSiap = $booking->updated_at->diffInDays(now());

            if (in_array($hariSejakSiap, [3, 7, 14])) {
                $notificationService->kirim(
                    $booking->user,
                    'reminder_pengambilan',
                    'Jangan lupa ambil hasil cetak',
                    "Hasil cetak {$booking->package->nama_paket} kamu sudah {$hariSejakSiap} hari siap, silakan diambil di studio.",
                    $booking->id
                );

                if ($hariSejakSiap === 14) {
                    $notificationService->kirimKeAdmin(
                        'reminder_pengambilan_admin',
                        'Cetak fisik lama belum diambil',
                        "{$booking->user->nama} belum ambil hasil {$booking->package->nama_paket} sudah 14 hari, mungkin perlu di-follow up manual.",
                        $booking->id
                    );
                }

                $terkirim++;
            }
        }

        $this->info("Reminder pengambilan terkirim untuk {$terkirim} booking.");
    }
}