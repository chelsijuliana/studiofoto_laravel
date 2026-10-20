<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    // Kirim ke 1 user tertentu
    public function kirim(User $user, string $type, string $judul, string $pesan, ?int $bookingId = null): void
    {
        AppNotification::create([
            'user_id' => $user->id,
            'booking_id' => $bookingId,
            'type' => $type,
            'judul' => $judul,
            'pesan' => $pesan,
            'is_read' => false,
        ]);

        // TODO: kirim push notification lewat FCM pakai $user->deviceTokens
        // Ditunda dulu sampai project Firebase disiapkan, tapi notifikasi
        // tetap tersimpan di database dan bisa dilihat lewat GET /api/notifications
    }

    // Kirim ke semua admin sekaligus (dipakai buat notif "booking baru", "chat baru", dll)
    public function kirimKeAdmin(string $type, string $judul, string $pesan, ?int $bookingId = null): void
    {
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $this->kirim($admin, $type, $judul, $pesan, $bookingId);
        }

        // ====================================================================
        //  NOTIFIKASI WHATSAPP (FONNTE) — TIDAK AKTIF, dikomen.
        //  Dulu setiap notifikasi admin juga otomatis dikirim ke WhatsApp,
        //  biar admin gak perlu buka Panel Admin terus buat mantau.
        // ====================================================================
        // $this->kirimWhatsAppKeAdmin($judul, $pesan, $bookingId);
    }

    // ============================================================================
    //  NOTIFIKASI WHATSAPP (FONNTE) — TIDAK AKTIF, dikomen isinya.
    //  Sengaja tidak dihapus, siapa tau suatu saat mau diaktifkan lagi.
    //  Dulu dipanggil dari kirimKeAdmin() untuk kirim pesan WhatsApp ke admin
    //  lewat Fonnte, sertakan link langsung ke Detail Booking kalau ada
    //  bookingId-nya.
    // ============================================================================
    // private function kirimWhatsAppKeAdmin(string $judul, string $pesan, ?int $bookingId = null): void
    // {
    //     $token = config('services.fonnte.token');
    //     $nomorAdmin = config('services.fonnte.admin_number');
    //
    //     // Kalau belum dikonfigurasi (misal pas development awal), skip diam-diam
    //     if (! $token || ! $nomorAdmin) {
    //         return;
    //     }
    //
    //     $teks = "🔔 *{$judul}*\n{$pesan}";
    //
    //     if ($bookingId) {
    //         $link = route('admin.bookings.show', $bookingId);
    //         $teks .= "\n\nLihat detail: {$link}";
    //     }
    //
    //     try {
    //         Http::withHeaders(['Authorization' => $token])
    //             ->post('https://api.fonnte.com/send', [
    //                 'target' => $nomorAdmin,
    //                 'message' => $teks,
    //             ]);
    //     } catch (\Exception $e) {
    //         // Kalau gagal kirim WA (misal koneksi/Fonnte lagi down), jangan sampai
    //         // ganggu proses utama (booking, chat, dll) — cukup dicatat di log
    //         Log::warning('Gagal kirim notifikasi WhatsApp ke admin: '.$e->getMessage());
    //     }
    // }
}