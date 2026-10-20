<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// ============================================================================
//  PAYMENT GATEWAY (MIDTRANS) — TIDAK DIPAKAI LAGI
//  Studio sekarang pakai sistem TRANSFER MANUAL (upload bukti transfer +
//  verifikasi admin). Kode Midtrans di bawah ini SENGAJA TIDAK DIHAPUS,
//  cuma dikomen, siapa tau suatu saat mau diaktifkan lagi.
// use Midtrans\Config;
// use Midtrans\Snap;
// use Midtrans\Transaction;
// ============================================================================

class PaymentService
{
    public function __construct(private NotificationService $notificationService)
    {
        // ====================================================================
        //  GATEWAY (MIDTRANS) — dikomen, tidak dipakai lagi
        // Config::$serverKey = config('services.midtrans.server_key');
        // Config::$isProduction = config('services.midtrans.is_production');
        // Config::$isSanitized = true;
        // Config::$is3ds = true;
        // ====================================================================
    }

    // ============================================================================
    //  GATEWAY (MIDTRANS) — dikomen, tidak dipakai lagi.
    //  Dulu dipanggil dari PaymentController::store() buat bikin transaksi
    //  Midtrans (DP/pelunasan) dan dapetin snap_token buat WebView Flutter.
    // ============================================================================
    // public function buatTransaksi(Booking $booking, string $jenis): array
    // {
    //     $jumlah = $jenis === 'dp'
    //         ? $booking->package->category->hitungDp($booking->total_harga)
    //         : $booking->sisaPelunasan();
    //
    //     // Kalau sebelumnya sudah pernah buat transaksi untuk jenis yang sama dan
    //     // masih 'pending' (belum expired/gagal/sukses), pakai lagi baris itu —
    //     // jangan bikin baris Payment baru tiap kali user pencet "Bayar Sekarang".
    //     // Ini juga yang bikin dulu muncul 2 baris "Down Payment (DP)" di riwayat.
    //     $payment = Payment::where('booking_id', $booking->id)
    //         ->where('jenis', $jenis)
    //         ->where('status', 'pending')
    //         ->latest()
    //         ->first();
    //
    //     if (! $payment) {
    //         $payment = Payment::create([
    //             'booking_id' => $booking->id,
    //             'jenis' => $jenis,
    //             'jumlah' => $jumlah,
    //             'status' => 'pending',
    //         ]);
    //
    //         $payment->update(['gateway_transaction_id' => 'BOOKING-'.$booking->id.'-'.$jenis.'-'.$payment->id]);
    //     }
    //
    //     $params = [
    //         'transaction_details' => [
    //             'order_id' => $payment->gateway_transaction_id,
    //             'gross_amount' => (int) $jumlah,
    //         ],
    //         'customer_details' => [
    //             'first_name' => $booking->user->nama,
    //             'email' => $booking->user->email,
    //             'phone' => $booking->user->no_hp,
    //         ],
    //     ];
    //
    //     // Midtrans mengizinkan minta snap_token baru untuk order_id yang sama
    //     // selama transaksi lamanya belum settlement/expire, jadi aman dipanggil ulang.
    //     $snapToken = Snap::getSnapToken($params);
    //
    //     return [
    //         'snap_token' => $snapToken,
    //         'payment_id' => $payment->id,
    //         'jumlah' => $jumlah,
    //     ];
    // }

    // ============================================================================
    //  GATEWAY (MIDTRANS) — dikomen, tidak dipakai lagi.
    //  Dulu dipanggil aktif dari Flutter setelah user kembali dari WebView
    //  pembayaran, buat cek status transaksi langsung ke Midtrans (pelengkap
    //  webhook, karena webhook Midtrans gak akan pernah nyampe kalau backend
    //  masih di localhost/tanpa tunnel publik).
    // ============================================================================
    // public function cekStatusTerbaru(Booking $booking): ?Payment
    // {
    //     $payment = Payment::where('booking_id', $booking->id)
    //         ->where('status', 'pending')
    //         ->latest()
    //         ->first();
    //
    //     if (! $payment || ! $payment->gateway_transaction_id) {
    //         return $payment;
    //     }
    //
    //     try {
    //         $status = Transaction::status($payment->gateway_transaction_id);
    //     } catch (\Exception $e) {
    //         Log::warning('Gagal cek status transaksi Midtrans: '.$e->getMessage());
    //
    //         return $payment;
    //     }
    //
    //     $transactionStatus = $status->transaction_status ?? null;
    //     $fraudStatus = $status->fraud_status ?? null;
    //
    //     if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
    //         if ($fraudStatus === 'accept' || $fraudStatus === null) {
    //             $this->tandaiBerhasil($payment);
    //         }
    //     } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
    //         $payment->update(['status' => 'failed']);
    //     }
    //
    //     return $payment->fresh();
    // }

    // ============================================================================
    //  GATEWAY (MIDTRANS) — dikomen, tidak dipakai lagi.
    //  Dulu dipanggil dari webhook Midtrans (POST /api/payments/callback).
    // ============================================================================
    // public function prosesNotifikasi(array $notification): void
    // {
    //     $orderId = $notification['order_id'];
    //     $transactionStatus = $notification['transaction_status'];
    //     $fraudStatus = $notification['fraud_status'] ?? null;
    //
    //     $payment = Payment::where('gateway_transaction_id', $orderId)->firstOrFail();
    //
    //     if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
    //         if ($fraudStatus === 'accept' || $fraudStatus === null) {
    //             $this->tandaiBerhasil($payment);
    //         }
    //     } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
    //         $payment->update(['status' => 'failed']);
    //     }
    // }

    // ============================================================================
    //  TRANSFER MANUAL — AKTIF (dipakai sekarang)
    //  Dipanggil dari PaymentController::store() setelah pelanggan upload
    //  bukti transfer. Bikin/pakai-lagi baris Payment dengan status 'pending'
    //  (menunggu diverifikasi admin), simpan foto bukti transfer-nya.
    // ============================================================================
    public function catatTransferManual(Booking $booking, string $jenis, $buktiTransferFile): Payment
    {
        $jumlah = $jenis === 'dp'
            ? $booking->package->category->hitungDp($booking->total_harga)
            : $booking->sisaPelunasan();

        // Sama seperti alur gateway dulu: kalau masih ada Payment 'pending'
        // untuk jenis yang sama, pakai lagi baris itu (misal pelanggan salah
        // upload lalu ajukan ulang), jangan numpuk baris baru terus.
        $payment = Payment::where('booking_id', $booking->id)
            ->where('jenis', $jenis)
            ->where('status', 'pending')
            ->latest()
            ->first();

        $path = $buktiTransferFile->store("payment_proofs/{$booking->id}", 'public');
        $buktiUrl = Storage::disk('public')->url($path);

        if ($payment) {
            $payment->update([
                'jumlah' => $jumlah,
                'metode_pembayaran' => 'transfer_manual',
                'bukti_transfer' => $buktiUrl,
                'catatan_admin' => null,
            ]);
        } else {
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'jenis' => $jenis,
                'jumlah' => $jumlah,
                'metode_pembayaran' => 'transfer_manual',
                'bukti_transfer' => $buktiUrl,
                'status' => 'pending',
            ]);
        }

        $jenisLabel = $jenis === 'dp' ? 'DP' : 'pelunasan';

        $this->notificationService->kirim(
            $booking->user,
            'bukti_transfer_terkirim',
            'Bukti transfer terkirim',
            "Bukti transfer {$jenisLabel} kamu sudah kami terima, menunggu diverifikasi admin.",
            $booking->id
        );

        $this->notificationService->kirimKeAdmin(
            'bukti_transfer_masuk',
            'Bukti transfer baru menunggu verifikasi',
            "{$booking->user->nama} upload bukti transfer {$jenisLabel} untuk booking {$booking->package->nama_paket}.",
            $booking->id
        );

        return $payment->fresh();
    }

    // ============================================================================
    //  TRANSFER MANUAL — AKTIF (dipakai sekarang)
    //  Dipanggil admin (BookingShow) saat menyetujui bukti transfer yang
    //  diupload pelanggan. Reuse tandaiBerhasil() yang sama dengan yang dulu
    //  dipakai gateway, karena logikanya (update status booking + kirim
    //  notifikasi) sama persis, cuma sumber "pembayaran berhasil"-nya beda.
    // ============================================================================
    public function setujuiPembayaranManual(Payment $payment): void
    {
        $this->tandaiBerhasil($payment);
    }

    // ============================================================================
    //  TRANSFER MANUAL — AKTIF (dipakai sekarang)
    //  Dipanggil admin (BookingShow) kalau bukti transfer yang diupload
    //  ternyata tidak valid/salah, supaya pelanggan tau harus upload ulang.
    // ============================================================================
    public function tolakPembayaranManual(Payment $payment, ?string $alasan = null): void
    {
        $payment->update([
            'status' => 'failed',
            'catatan_admin' => $alasan,
        ]);

        $jenisLabel = $payment->jenis === 'dp' ? 'DP' : 'pelunasan';
        $booking = $payment->booking;

        $this->notificationService->kirim(
            $booking->user,
            'bukti_transfer_ditolak',
            'Bukti transfer ditolak',
            "Bukti transfer {$jenisLabel} kamu untuk booking {$booking->package->nama_paket} ditolak admin".
                ($alasan ? ": {$alasan}." : '. Silakan upload ulang bukti transfer yang valid.'),
            $booking->id
        );
    }

    // Dipakai bareng oleh transfer manual (setujuiPembayaranManual) dan dulu
    // juga dipakai gateway (buatTransaksi punya versi Midtrans-nya di atas,
    // sekarang dikomen) — method ini sendiri NETRAL, tidak bergantung ke
    // Midtrans sama sekali, jadi tetap dipakai apa adanya.
    private function tandaiBerhasil(Payment $payment): void
    {
        // Jaga-jaga kalau ada 2 proses yang sama-sama berhasil memproses —
        // jangan kirim notifikasi dobel / update status booking dua kali.
        if ($payment->status === 'success') {
            return;
        }

        $payment->update([
            'status' => 'success',
            'paid_at' => now(),
        ]);

        $booking = $payment->booking;

        if ($payment->jenis === 'dp') {
            $booking->update(['status' => 'confirmed']);
        }

        $jenisLabel = $payment->jenis === 'dp' ? 'DP' : 'pelunasan';

        $this->notificationService->kirim(
            $booking->user,
            'pembayaran_berhasil',
            "Pembayaran {$jenisLabel} berhasil",
            "Pembayaran {$jenisLabel} sebesar Rp".number_format($payment->jumlah, 0, ',', '.')." untuk booking {$booking->package->nama_paket} berhasil.",
            $booking->id
        );

        $this->notificationService->kirimKeAdmin(
            'pembayaran_masuk',
            "Pembayaran {$jenisLabel} masuk",
            "{$booking->user->nama} baru saja bayar {$jenisLabel} untuk booking {$booking->package->nama_paket}.",
            $booking->id
        );
        // Kalau jenis == pelunasan, status booking tetap "confirmed",
        // cuma sisa_pelunasan otomatis jadi 0 karena dihitung dari tabel payments.
    }
}