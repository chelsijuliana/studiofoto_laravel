<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class BatalkanBookingKedaluwarsa extends Command
{
    protected $signature = 'booking:batalkan-kedaluwarsa';
    protected $description = 'Membatalkan otomatis booking yang belum dibayar DP dalam 24 jam, membebaskan slot untuk pelanggan lain';

    public function handle(NotificationService $notificationService): void
    {
        //$batasWaktu = now()->subMinutes(1);
        $batasWaktu = now()->subHours(24);
        
        $bookings = Booking::where('status', 'menunggu_pembayaran_dp')
            ->where('created_at', '<=', $batasWaktu)
            ->with('user', 'package')
            ->get();

        foreach ($bookings as $booking) {
            $booking->update(['status' => 'dibatalkan']);

            $notificationService->kirim(
                $booking->user,
                'booking_dibatalkan_otomatis',
                'Booking dibatalkan otomatis',
                "Booking {$booking->package->nama_paket} kamu dibatalkan otomatis karena DP tidak dibayar dalam 24 jam. Silakan booking ulang jika masih berminat.",
                $booking->id
            );

            $notificationService->kirimKeAdmin(
                'booking_dibatalkan_otomatis',
                'Booking dibatalkan otomatis (tidak dibayar)',
                "Booking {$booking->user->nama} - {$booking->package->nama_paket} dibatalkan otomatis karena DP tidak dibayar dalam 24 jam.",
                $booking->id
            );
        }

        $this->info("Selesai. {$bookings->count()} booking dibatalkan otomatis karena tidak dibayar.");
    }
}