<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ReportIndex extends Component
{
    public string $mode = 'bulan';
    public int $bulan;
    public int $tahun;

    public function mount()
    {
        $this->bulan = now()->month;
        $this->tahun = now()->year;
    }

    /**
     * Query dasar: booking berdasarkan tanggal sesi (bukan paid_at),
     * sehingga booking Agustus tetap muncul meski DP dibayar di Juli.
     * Tetap filter hanya booking yang sudah ada pembayaran success.
     */
    private function baseQuery()
    {
        $query = Booking::whereHas('payments', fn($q) => $q->where('status', 'success'))
            ->whereHas('sessions', function ($q) {
                $q->whereYear('tanggal', $this->tahun);
                if ($this->mode === 'bulan') {
                    $q->whereMonth('tanggal', $this->bulan);
                }
            });

        return $query;
    }

    /**
     * Semua booking yang sesinya jatuh di periode ini, KECUALI yang
     * dibatalkan — dipakai untuk menghitung Total Piutang (uang yang
     * masih harus ditagih, baik dari booking yang belum bayar sama
     * sekali maupun yang baru bayar DP tapi belum lunas).
     */
    private function semuaBookingAktifQuery()
    {
        return Booking::whereHas('sessions', function ($q) {
                $q->whereYear('tanggal', $this->tahun);
                if ($this->mode === 'bulan') {
                    $q->whereMonth('tanggal', $this->bulan);
                }
            })
            ->where('status', '!=', 'dibatalkan');
    }

    /**
     * Query pembayaran untuk metrik keuangan (tetap pakai paid_at agar akurat).
     */
    private function paymentQuery()
    {
        // Ambil booking_id yang sesuai periode sesi
        $bookingIds = $this->baseQuery()->pluck('id');

        return Payment::where('payments.status', 'success')
            ->whereIn('booking_id', $bookingIds);
    }

    public function getMetricsProperty(): array
    {
        $totalPembayaran = $this->paymentQuery()->sum('jumlah');

        // Total Piutang = nilai penuh SEMUA booking aktif periode ini,
        // dikurangi SEMUA pembayaran sukses (DP + Pelunasan) yang sudah
        // masuk untuk booking-booking itu. Jadi sisa DP yang belum lunas
        // ikut terhitung di sini, tidak "hilang" seperti logika lama.
        $semuaBookingAktifIds = $this->semuaBookingAktifQuery()->pluck('id');
        $totalNilaiBookingAktif = Booking::whereIn('id', $semuaBookingAktifIds)->sum('total_harga');
        $totalTerbayarBookingAktif = Payment::where('status', 'success')
            ->whereIn('booking_id', $semuaBookingAktifIds)
            ->sum('jumlah');
        $totalPiutang = $totalNilaiBookingAktif - $totalTerbayarBookingAktif;

        // "Booking Lunas" = sudah bayar DP + Pelunasan penuh (terbayar >= total_harga).
        // "Booking Belum Lunas" = masih ada sisa pelunasan ATAU belum bayar sama sekali
        // (terbayar < total_harga). Dua-duanya saling melengkapi, dihitung dari
        // set booking yang sama (semua booking aktif periode ini).
        $daftarBookingAktif = $this->semuaBookingAktifQuery()
            ->get(['id', 'total_harga'])
            ->map(function ($b) {
                $b->terbayar = Payment::where('booking_id', $b->id)->where('status', 'success')->sum('jumlah');
                return $b;
            });

        $bookingLunas = $daftarBookingAktif->filter(fn ($b) => $b->terbayar >= $b->total_harga)->count();
        $bookingBelumLunas = $daftarBookingAktif->filter(fn ($b) => $b->terbayar < $b->total_harga)->count();

        return [
            'total_booking'       => $bookingLunas,
            'total_transaksi'     => $totalPembayaran,
            'booking_belum_lunas' => $bookingBelumLunas,
            'total_piutang'       => $totalPiutang,
        ];
    }

    public function getPerKategoriProperty()
    {
        $bookingIds = $this->baseQuery()->pluck('id');

        return Payment::where('payments.status', 'success')
            ->whereIn('payments.booking_id', $bookingIds)
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('service_packages', 'bookings.package_id', '=', 'service_packages.id')
            ->join('service_categories', 'service_packages.category_id', '=', 'service_categories.id')
            ->selectRaw('service_categories.nama_kategori, count(distinct bookings.id) as jumlah_booking, sum(payments.jumlah) as pendapatan')
            ->groupBy('service_categories.nama_kategori')
            ->orderByDesc('pendapatan')
            ->get();
    }

    /**
     * Ambil 1 tanggal sesi representatif per booking dalam periode ini —
     * yaitu tanggal sesi paling awal yang jatuh di periode yang dipilih.
     * Dipakai supaya booking dengan >1 sesi (misal Wedding: akad + pesta
     * di tanggal berbeda) tidak menyumbang total_harga-nya lebih dari
     * sekali ke chart tren.
     */
    private function tanggalRepresentatifPerBooking($bookingIds)
    {
        $query = DB::table('booking_sessions')
            ->whereIn('booking_id', $bookingIds)
            ->whereYear('tanggal', $this->tahun);

        if ($this->mode === 'bulan') {
            $query->whereMonth('tanggal', $this->bulan);
        }

        return $query->orderBy('tanggal')
            ->get(['booking_id', 'tanggal'])
            ->unique('booking_id')
            ->pluck('tanggal', 'booking_id');
    }

    public function getTrenProperty()
    {
        $bookingIds = $this->baseQuery()->pluck('id');
        $tanggalPerBooking = $this->tanggalRepresentatifPerBooking($bookingIds);
        $totalHargaPerBooking = Booking::whereIn('id', $bookingIds)->pluck('total_harga', 'id');

        // Kelompokkan total_harga per booking (dihitung sekali per booking)
        // ke label hari (mode bulan) atau bulan (mode tahun).
        $grouped = [];
        foreach ($tanggalPerBooking as $bookingId => $tanggal) {
            $label = $this->mode === 'bulan'
                ? (int) \Carbon\Carbon::parse($tanggal)->format('j')
                : (int) \Carbon\Carbon::parse($tanggal)->format('n');

            $grouped[$label] = ($grouped[$label] ?? 0) + (float) ($totalHargaPerBooking[$bookingId] ?? 0);
        }
        ksort($grouped);

        return collect($grouped)
            ->map(fn ($total, $label) => (object) ['label' => $label, 'total' => $total])
            ->values();
    }

    public function export()
    {
        $filename = "laporan-{$this->tahun}" . ($this->mode === 'bulan' ? "-{$this->bulan}" : '') . '.csv';

        // Semua booking yang sesinya jatuh di periode ini, apapun status
        // pembayarannya (belum bayar/DP/lunas), KECUALI yang dibatalkan.
        $semuaBookingIds = Booking::whereHas('sessions', function ($q) {
                $q->whereYear('tanggal', $this->tahun);
                if ($this->mode === 'bulan') {
                    $q->whereMonth('tanggal', $this->bulan);
                }
            })
            ->where('status', '!=', 'dibatalkan')
            ->pluck('id');

        // Info sesi (tanggal + jam) per booking, buat kolom "Tanggal Sesi"/"Jam
        // Sesi". Diambil manual di PHP (bukan MIN() SQL) supaya tanggal & jam
        // yang diambil tetap dari baris sesi yang sama (sesi paling awal
        // booking itu), bukan MIN() masing-masing kolom secara terpisah yang
        // bisa gak nyambung.
        $sesiPerBooking = [];
        DB::table('booking_sessions')
            ->whereIn('booking_id', $semuaBookingIds)
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get(['booking_id', 'tanggal', 'jam_mulai'])
            ->each(function ($s) use (&$sesiPerBooking) {
                if (! isset($sesiPerBooking[$s->booking_id])) {
                    $sesiPerBooking[$s->booking_id] = ['tanggal' => $s->tanggal, 'jam' => $s->jam_mulai];
                }
            });

        // Data booking (satu baris per booking) buat tabel "Laporan Transaksi".
        // "Total Bayar" di sini pakai total_harga booking (harga paket yang
        // ditetapkan, sama seperti kolom di Manajemen Booking) — BUKAN
        // dijumlah dari pembayaran yang beneran udah masuk (DP+Pelunasan),
        // karena tabel ini fungsinya buat lihat total NILAI transaksi
        // (berapa booking & berapa nilainya) yang terjadi bulan itu.
        $bookingRows = Booking::whereIn('bookings.id', $semuaBookingIds)
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->join('service_packages', 'bookings.package_id', '=', 'service_packages.id')
            ->join('service_categories', 'service_packages.category_id', '=', 'service_categories.id')
            ->select(
                'bookings.id as booking_id',
                'bookings.total_harga',
                'users.nama',
                'users.no_hp',
                'service_categories.nama_kategori',
                'service_packages.nama_paket'
            )
            ->get();

        // Jumlah booking per layanan/kategori (misal: Wedding 3 booking, Pre
        // Wedding 2 booking, dst), dihitung dari set booking yang sama dengan
        // tabel "Laporan Transaksi" di atas — diurutkan dari yang paling banyak.
        $jumlahPerKategori = $bookingRows
            ->groupBy('nama_kategori')
            ->map(fn ($grup) => $grup->count())
            ->sortDesc();

        return response()->streamDownload(function () use ($sesiPerBooking, $bookingRows, $jumlahPerKategori) {
            $handle = fopen('php://output', 'w');

            // Format nominal jadi "Rp. 1.000.000" biar langsung kebaca di CSV.
            $formatRupiah = fn ($v) => 'Rp. ' . number_format($v, 0, ',', '.');

            // ── Tabel: ringkasan per booking ──
            fputcsv($handle, ['Laporan Transaksi']);
            fputcsv($handle, ['Nama Pelanggan', 'No Hp', 'Layanan', 'Paket', 'Tanggal Sesi', 'Jam Sesi', 'Total Bayar']);
            $grandTotal = 0;
            foreach ($bookingRows as $b) {
                $sesi = $sesiPerBooking[$b->booking_id] ?? null;
                $totalBooking = $b->total_harga;
                $grandTotal += $totalBooking;
                fputcsv($handle, [
                    $b->nama,
                    $b->no_hp,
                    $b->nama_kategori,
                    $b->nama_paket,
                    $sesi['tanggal'] ?? '',
                    $sesi['jam'] ?? '',
                    $formatRupiah($totalBooking),
                ]);
            }
            fputcsv($handle, ['', '', '', '', '', 'TOTAL', $formatRupiah($grandTotal)]);

            // ── Pemisah + Tabel: jumlah booking per layanan ──
            fputcsv($handle, []);
            fputcsv($handle, ['Jumlah Booking per Layanan']);
            fputcsv($handle, ['Layanan', 'Jumlah Booking']);
            foreach ($jumlahPerKategori as $namaKategori => $jumlah) {
                fputcsv($handle, [$namaKategori, $jumlah]);
            }
            fputcsv($handle, ['TOTAL', $bookingRows->count()]);

            fclose($handle);
        }, $filename);
    }

    public function render()
    {
        $tren = $this->tren;

        // Label chart dibikin lebih mudah dibaca: angka bulan (1-12) jadi nama
        // bulan singkat, angka tanggal (1-31) tetap angka tapi dari int biar rapi.
        $trenLabels = $this->mode === 'tahun'
            ? $tren->pluck('label')->map(fn($m) => \Carbon\Carbon::create(2000, (int) $m, 1)->translatedFormat('M'))
            : $tren->pluck('label')->map(fn($d) => (int) $d);

        return view('livewire.admin.report-index', [
            'metrics'     => $this->metrics,
            'perKategori' => $this->perKategori,
            'tren'        => $tren,
            'trenLabels'  => $trenLabels,
            'trenValues'  => $tren->pluck('total')->map(fn($v) => (float) $v),
        ]);
    }
}