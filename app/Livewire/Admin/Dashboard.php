<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $bookingHariIni = Booking::whereHas('sessions', fn ($q) => $q->whereDate('tanggal', today()))
            ->where('status', '!=', 'dibatalkan')
            ->count();

        // Booking yang perlu direspon: status menunggu_konfirmasi (biaya luar kota)
        // ATAU ada chat dari pelanggan yang belum dibaca. Untuk tiap booking juga
        // diambil info tambahan (sesi terdekat, jumlah & isi pesan terakhir yang
        // belum dibaca) supaya daftarnya lebih informatif, bukan cuma nama+status.
        $perluDirespon = Booking::with(['user', 'package.category'])
            ->with(['sessions' => fn ($q) => $q->orderBy('tanggal')->limit(1)])
            ->where(function ($q) {
                $q->where('status', 'menunggu_konfirmasi')
                    ->orWhereHas('chatMessages', fn ($q2) => $q2->where('sender_type', 'customer')->where('is_read', false));
            })
            ->withCount(['chatMessages as chat_belum_dibaca' => fn ($q) => $q->where('sender_type', 'customer')->where('is_read', false)])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($booking) {
                $pesanTerakhir = $booking->chat_belum_dibaca > 0
                    ? $booking->chatMessages()
                        ->where('sender_type', 'customer')
                        ->where('is_read', false)
                        ->latest()
                        ->first()
                    : null;

                $booking->pesan_terakhir = $pesanTerakhir?->pesan;
                $booking->waktu_pesan_terakhir = $pesanTerakhir?->created_at;

                return $booking;
            });

        // Basis "bulan ini" di sini dan di bawah dihitung dari TANGGAL SESI
        // (kapan acara/sesi fotonya berlangsung), bukan dari kapan booking-nya
        // dibuat. Jadi booking yang dipesan bulan lalu tapi sesinya bulan ini
        // tetap terhitung — begini juga cara Laporan Transaksi menghitung.
        $filterSesiBulanIni = fn ($q) => $q->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year);

        // Total nilai transaksi bulan ini: dijumlahkan dari total_harga SEMUA
        // booking yang sesinya jatuh bulan ini, apapun status pembayarannya
        // (sudah lunas, baru DP, atau belum bayar sama sekali).
        $pendapatanBulanIni = Booking::whereHas('sessions', $filterSesiBulanIni)
            ->where('status', '!=', 'dibatalkan')
            ->sum('total_harga');

        $totalBookingBulanIni = Booking::whereHas('sessions', $filterSesiBulanIni)
            ->where('status', '!=', 'dibatalkan')
            ->count();

        $bookingPerKategori = Booking::whereHas('sessions', $filterSesiBulanIni)
            ->where('status', '!=', 'dibatalkan')
            ->join('service_packages', 'bookings.package_id', '=', 'service_packages.id')
            ->join('service_categories', 'service_packages.category_id', '=', 'service_categories.id')
            ->selectRaw('service_categories.nama_kategori, count(*) as jumlah')
            ->groupBy('service_categories.nama_kategori')
            ->get();

        return view('livewire.admin.dashboard', [
            'bookingHariIni' => $bookingHariIni,
            'perluDirespon' => $perluDirespon,
            'pendapatanBulanIni' => $pendapatanBulanIni,
            'totalBookingBulanIni' => $totalBookingBulanIni,
            'bookingPerKategori' => $bookingPerKategori,
        ]);
    }
}