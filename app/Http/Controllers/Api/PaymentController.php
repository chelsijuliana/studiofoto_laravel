<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService)
    {
    }

    // POST /api/bookings/{booking}/payments  { "jenis": "dp"|"pelunasan", "bukti_transfer": <file> }
    // TRANSFER MANUAL — AKTIF. Pelanggan upload bukti transfer, tersimpan
    // dengan status 'pending' (menunggu diverifikasi admin lewat Panel Admin).
    public function store(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            abort(403, 'Kamu tidak punya akses ke booking ini.');
        }

        $request->validate([
            'jenis' => 'required|in:dp,pelunasan',
            'bukti_transfer' => 'required|image|max:5120', // maks 5MB
        ]);

        if ($request->jenis === 'dp' && $booking->status !== 'menunggu_pembayaran_dp') {
            return response()->json(['message' => 'Booking ini tidak sedang menunggu pembayaran DP.'], 422);
        }

        if ($request->jenis === 'pelunasan' && $booking->sisaPelunasan() <= 0) {
            return response()->json(['message' => 'Booking ini sudah lunas.'], 422);
        }

        $payment = $this->paymentService->catatTransferManual($booking, $request->jenis, $request->file('bukti_transfer'));

        return response()->json([
            'message' => 'Bukti transfer berhasil dikirim, menunggu verifikasi admin.',
            'payment' => $payment,
        ], 201);

        // ====================================================================
        //  GATEWAY (MIDTRANS) — dikomen, tidak dipakai lagi.
        //  Dulu method store() ini langsung minta snap_token ke Midtrans,
        //  bukan nunggu upload bukti transfer.
        // ====================================================================
        // $request->validate([
        //     'jenis' => 'required|in:dp,pelunasan',
        // ]);
        //
        // if ($request->jenis === 'dp' && $booking->status !== 'menunggu_pembayaran_dp') {
        //     return response()->json(['message' => 'Booking ini tidak sedang menunggu pembayaran DP.'], 422);
        // }
        //
        // if ($request->jenis === 'pelunasan' && $booking->sisaPelunasan() <= 0) {
        //     return response()->json(['message' => 'Booking ini sudah lunas.'], 422);
        // }
        //
        // $result = $this->paymentService->buatTransaksi($booking, $request->jenis);
        //
        // return response()->json($result);
    }

    // ============================================================================
    //  GATEWAY (MIDTRANS) — TIDAK AKTIF, dikomen isinya.
    //  Dulu dipanggil otomatis oleh server Midtrans lewat webhook.
    //  Endpoint-nya sengaja dibiarkan ada (tidak dihapus dari routes) supaya
    //  gampang diaktifkan lagi kalau nanti balik pakai payment gateway.
    // ============================================================================
    public function callback(Request $request)
    {
        // $this->paymentService->prosesNotifikasi($request->all());

        return response()->json(['message' => 'Payment gateway sedang tidak aktif, studio memakai transfer manual.']);
    }

    // ============================================================================
    //  GATEWAY (MIDTRANS) — TIDAK AKTIF, dikomen isinya.
    //  Dulu dipanggil Flutter setiap kali user balik dari halaman WebView
    //  pembayaran, buat cek status transaksi ke Midtrans secara aktif.
    //  Sudah tidak dipanggil lagi dari Flutter sejak pindah ke transfer manual
    //  (status pembayaran sekarang diverifikasi manual oleh admin).
    // ============================================================================
    public function checkStatus(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            abort(403, 'Kamu tidak punya akses ke booking ini.');
        }

        // $payment = $this->paymentService->cekStatusTerbaru($booking);
        //
        // return response()->json([
        //     'message' => 'Status pembayaran diperbarui',
        //     'payment' => $payment,
        //     'booking' => $booking->fresh()->load('payments'),
        // ]);

        return response()->json([
            'message' => 'Payment gateway sedang tidak aktif, studio memakai transfer manual.',
            'booking' => $booking->fresh()->load('payments'),
        ]);
    }
}