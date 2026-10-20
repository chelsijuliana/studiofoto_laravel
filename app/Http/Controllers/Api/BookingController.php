<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingSession;
use App\Models\ManualBlock;
use App\Models\OperatingHour;
use App\Models\PackageAddon;
use App\Models\ServicePackage;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    // GET /api/bookings/available-dates?package_id=1&month=7&year=2026
    public function availableDates(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:service_packages,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
        ]);

        $package = ServicePackage::with('category')->findOrFail($request->package_id);
        $category = $package->category;

        $butuhRuangan = $package->atribut_lain['butuh_ruangan'] ?? $category->butuh_ruangan;

        $startDate = Carbon::create($request->year, $request->month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $result = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $result[] = $this->checkDateAvailability($date->copy(), $category, $package, $butuhRuangan);
        }

        return response()->json($result);
    }

    // GET /api/bookings/available-times?package_id=1&tanggal=2026-07-20
    public function availableTimes(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:service_packages,id',
            'tanggal' => 'required|date',
        ]);

        $package = ServicePackage::with('category')->findOrFail($request->package_id);
        $category = $package->category;
        $tanggal = Carbon::parse($request->tanggal);

        $butuhRuangan = $package->atribut_lain['butuh_ruangan'] ?? $category->butuh_ruangan;

        if (! $butuhRuangan) {
            return response()->json([
                'perlu_input_manual' => true,
                'slots' => [],
            ]);
        }

        $operatingHour = OperatingHour::where('hari', $tanggal->dayOfWeek)->first();
        if (! $operatingHour || $operatingHour->is_tutup) {
            return response()->json(['perlu_input_manual' => false, 'slots' => []]);
        }

        $bufferMenit = 15;
        $durasi = $package->durasi_menit ?? 30;

        $jamBuka = Carbon::parse($request->tanggal.' '.$operatingHour->jam_buka);
        $jamTutup = Carbon::parse($request->tanggal.' '.$operatingHour->jam_tutup);

        $sesiTerpakai = BookingSession::whereDate('tanggal', $tanggal->toDateString())
            ->whereHas('booking', function ($q) {
                $q->where('status', '!=', 'dibatalkan')
                    ->whereHas('package.category', fn ($q2) => $q2->where('butuh_ruangan', true));
            })
            ->orderBy('jam_mulai')
            ->get();

        $rentangTerpakai = $sesiTerpakai->map(function ($sesi) use ($jamBuka, $bufferMenit) {
            $selesai = $sesi->jam_selesai
                ? Carbon::parse($sesi->tanggal->toDateString().' '.$sesi->jam_selesai)
                : Carbon::parse($sesi->tanggal->toDateString().' '.$sesi->jam_mulai);

            return [
                'mulai' => $jamBuka->diffInMinutes(Carbon::parse($sesi->tanggal->toDateString().' '.$sesi->jam_mulai), false),
                'selesai' => $jamBuka->diffInMinutes($selesai, false) + $bufferMenit,
            ];
        })->sortBy('mulai')->values();

        $totalMenitOperasional = $jamBuka->diffInMinutes($jamTutup);
        $stepMenit = 15;
        $slots = [];

        $startMinimal = 0;
        if ($tanggal->isToday()) {
            $menitSejakBuka = $jamBuka->diffInMinutes(now(), false);
            if ($menitSejakBuka > 0) {
                $startMinimal = (int) (ceil($menitSejakBuka / $stepMenit) * $stepMenit);
            }
        }

        for ($start = $startMinimal; $start + $durasi <= $totalMenitOperasional; $start += $stepMenit) {
            $end = $start + $durasi;

            $bentrok = $rentangTerpakai->contains(function ($r) use ($start, $end) {
                return $start < $r['selesai'] && $end > $r['mulai'];
            });

            if (! $bentrok) {
                $slots[] = [
                    'jam_mulai' => $jamBuka->copy()->addMinutes($start)->format('H:i'),
                    'jam_selesai' => $jamBuka->copy()->addMinutes($end)->format('H:i'),
                ];
            }
        }

        return response()->json([
            'perlu_input_manual' => false,
            'slots' => $slots,
        ]);
    }

    // POST /api/bookings
    public function store(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:service_packages,id',
            'sessions' => 'required|array|min:1',
            'sessions.*.jenis_sesi' => 'required|in:akad,pesta,utama',
            'sessions.*.tanggal' => 'required|date',
            'sessions.*.jam_mulai' => 'required',
            'sessions.*.alamat' => 'nullable|string',
            'sessions.*.lokasi_lat' => 'nullable|numeric',
            'sessions.*.lokasi_lng' => 'nullable|numeric',
            'sessions.*.catatan' => 'nullable|string',
            'sessions.*.perlu_luar_kota' => 'nullable|in:true,false,1,0',
            'jumlah_orang' => 'nullable|integer|min:1',
            'tambahan_sesi' => 'nullable|integer|min:0',
            'addon_ids' => 'nullable|array',
            'addon_ids.*' => 'exists:package_addons,id',
            'catatan' => 'nullable|string',
        ]);

        $package = ServicePackage::with('category')->findOrFail($request->package_id);
        $category = $package->category;
        $butuhRuangan = $package->atribut_lain['butuh_ruangan'] ?? $category->butuh_ruangan;

        foreach ($request->sessions as $sesi) {
            $errorKuota = $this->cekKuotaTanggal($sesi['tanggal'], $category);
            if ($errorKuota) {
                return response()->json(['message' => $errorKuota], 422);
            }

            if ($butuhRuangan) {
                $jamSelesai = Carbon::parse($sesi['jam_mulai'])->addMinutes($package->durasi_menit ?? 30);
                $bentrok = $this->cekBentrokRuangan($sesi['tanggal'], $sesi['jam_mulai'], $jamSelesai->format('H:i'));
                if ($bentrok) {
                    return response()->json(['message' => 'Slot jam yang dipilih sudah terpakai, silakan pilih ulang.'], 422);
                }
            }
        }

        $totalHarga = $package->harga;
        $adaKomponenNego = false;

        $selectedAddons = PackageAddon::whereIn('id', $request->addon_ids ?? [])->get();

        foreach ($selectedAddons as $addon) {
            if ($addon->tipe === 'flat') {
                $totalHarga += $addon->harga;
            } elseif ($addon->tipe === 'nego') {
                $adaKomponenNego = true;
            }
        }

        if ($request->filled('tambahan_sesi') && $request->tambahan_sesi > 0) {
            $addonSesi = $category->addons()->where('tipe', 'per_sesi')->first();
            if ($addonSesi) {
                $totalHarga += $addonSesi->harga * $request->tambahan_sesi;
            }
        }

        if ($request->filled('jumlah_orang') && $package->max_orang && $request->jumlah_orang > $package->max_orang) {
            $kelebihanOrang = $request->jumlah_orang - $package->max_orang;
            $addonOrang = $category->addons()->where('tipe', 'per_orang')->first();
            if ($addonOrang) {
                $totalHarga += $addonOrang->harga * $kelebihanOrang;
            }
        }

        foreach ($request->sessions as $sesi) {
            if (filter_var($sesi['perlu_luar_kota'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                $adaKomponenNego = true;
            }
        }

        if ($adaKomponenNego) {
            $status = 'menunggu_konfirmasi';
        } elseif ($category->dp_type === 'none') {
            $status = 'confirmed';
        } else {
            $status = 'menunggu_pembayaran_dp';
        }

        $booking = DB::transaction(function () use ($request, $package, $totalHarga, $status, $selectedAddons, $category) {
            $booking = Booking::create([
                'user_id' => $request->user()->id,
                'package_id' => $package->id,
                'status' => $status,
                'total_harga' => $totalHarga,
                'catatan' => $request->catatan,
            ]);

            foreach ($request->sessions as $sesi) {
                $jamSelesai = isset($sesi['jam_mulai']) && $package->durasi_menit
                    ? Carbon::parse($sesi['jam_mulai'])->addMinutes($package->durasi_menit)->format('H:i')
                    : null;

                $booking->sessions()->create([
                    'jenis_sesi' => $sesi['jenis_sesi'],
                    'tanggal' => $sesi['tanggal'],
                    'jam_mulai' => $sesi['jam_mulai'],
                    'jam_selesai' => $jamSelesai,
                    'lokasi_lat' => $sesi['lokasi_lat'] ?? null,
                    'lokasi_lng' => $sesi['lokasi_lng'] ?? null,
                    'alamat' => $sesi['alamat'] ?? null,
                    'catatan' => $sesi['catatan'] ?? null,
                    'perlu_luar_kota' => filter_var($sesi['perlu_luar_kota'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ]);
            }

            $booking->items()->create([
                'keterangan' => "Paket {$package->nama_paket}",
                'qty' => 1,
                'harga_satuan' => $package->harga,
                'subtotal' => $package->harga,
            ]);

            foreach ($selectedAddons as $addon) {
                if ($addon->tipe === 'flat') {
                    $booking->items()->create([
                        'keterangan' => $addon->nama_addon,
                        'qty' => 1,
                        'harga_satuan' => $addon->harga,
                        'subtotal' => $addon->harga,
                    ]);
                } elseif ($addon->tipe === 'nego') {
                    $booking->items()->create([
                        'keterangan' => $addon->nama_addon.' (menunggu konfirmasi admin)',
                        'qty' => 1,
                        'harga_satuan' => 0,
                        'subtotal' => 0,
                    ]);
                }
            }

            if ($request->filled('tambahan_sesi') && $request->tambahan_sesi > 0) {
                $addonSesi = $category->addons()->where('tipe', 'per_sesi')->first();
                if ($addonSesi) {
                    $booking->items()->create([
                        'keterangan' => 'Tambahan sesi',
                        'qty' => $request->tambahan_sesi,
                        'harga_satuan' => $addonSesi->harga,
                        'subtotal' => $addonSesi->harga * $request->tambahan_sesi,
                    ]);
                }
            }

            if ($request->filled('jumlah_orang') && $package->max_orang && $request->jumlah_orang > $package->max_orang) {
                $kelebihanOrang = $request->jumlah_orang - $package->max_orang;
                $addonOrang = $category->addons()->where('tipe', 'per_orang')->first();
                if ($addonOrang) {
                    $booking->items()->create([
                        'keterangan' => 'Tambahan orang',
                        'qty' => $kelebihanOrang,
                        'harga_satuan' => $addonOrang->harga,
                        'subtotal' => $addonOrang->harga * $kelebihanOrang,
                    ]);
                }
            }

            return $booking;
        });

        $dpAmount = $category->hitungDp($totalHarga);

        $this->notificationService->kirim(
            $request->user(),
            'booking_terkirim',
            'Booking terkirim',
            "Booking {$package->nama_paket} kamu sudah diterima, status: {$status}.",
            $booking->id
        );

        $this->notificationService->kirimKeAdmin(
            'booking_baru',
            'Booking baru masuk',
            "{$request->user()->nama} baru saja booking {$package->nama_paket}.",
            $booking->id
        );

        return response()->json([
            'message' => 'Booking berhasil dibuat',
            'booking' => $booking->load('sessions'),
            'dp_amount' => $status === 'menunggu_pembayaran_dp' ? $dpAmount : null,
        ], 201);
    }

    // PATCH /api/bookings/{booking}/confirm-price — khusus admin, buat konfirmasi harga nego (luar kota)
    public function confirmPrice(Request $request, Booking $booking)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang bisa konfirmasi harga.');
        }

        if ($booking->status !== 'menunggu_konfirmasi') {
            return response()->json(['message' => 'Booking ini tidak sedang menunggu konfirmasi harga.'], 422);
        }

        $request->validate([
            'biaya_tambahan' => 'required|numeric|min:0',
        ]);

        $booking->update([
            'total_harga' => $booking->total_harga + $request->biaya_tambahan,
            'status' => 'menunggu_pembayaran_dp',
        ]);

        $itemNego = $booking->items()->where('keterangan', 'like', '%menunggu konfirmasi admin%')->first();
        if ($itemNego) {
            $itemNego->update([
                'keterangan' => str_replace(' (menunggu konfirmasi admin)', '', $itemNego->keterangan),
                'harga_satuan' => $request->biaya_tambahan,
                'subtotal' => $request->biaya_tambahan,
            ]);
        } else {
            $booking->items()->create([
                'keterangan' => 'Biaya luar kota',
                'qty' => 1,
                'harga_satuan' => $request->biaya_tambahan,
                'subtotal' => $request->biaya_tambahan,
            ]);
        }

        $this->notificationService->kirim(
            $booking->user,
            'harga_dikonfirmasi',
            'Harga final dikonfirmasi',
            "Admin telah mengonfirmasi biaya tambahan sebesar Rp".number_format($request->biaya_tambahan, 0, ',', '.').". Total booking sekarang Rp".number_format($booking->total_harga, 0, ',', '.').".",
            $booking->id
        );

        return response()->json([
            'message' => 'Harga berhasil dikonfirmasi',
            'booking' => $booking,
        ]);
    }

    private function cekKuotaTanggal(string $tanggal, $category): ?string
    {
        if ($category->kuota_harian === null) {
            return null;
        }

        $jumlah = BookingSession::whereDate('tanggal', $tanggal)
            ->whereHas('booking', function ($q) use ($category) {
                $q->where('status', '!=', 'dibatalkan')
                    ->whereHas('package', fn ($q2) => $q2->where('category_id', $category->id));
            })
            ->count();

        return $jumlah >= $category->kuota_harian
            ? "Kuota booking untuk tanggal {$tanggal} sudah penuh."
            : null;
    }

    private function cekBentrokRuangan(string $tanggal, string $jamMulai, string $jamSelesai): bool
    {
        $sesiTerpakai = BookingSession::whereDate('tanggal', $tanggal)
            ->whereHas('booking', function ($q) {
                $q->where('status', '!=', 'dibatalkan')
                    ->whereHas('package.category', fn ($q2) => $q2->where('butuh_ruangan', true));
            })
            ->get();

        $mulaiBaru = Carbon::parse($jamMulai);
        $selesaiBaru = Carbon::parse($jamSelesai)->addMinutes(15);

        foreach ($sesiTerpakai as $sesi) {
            $mulaiLama = Carbon::parse($sesi->jam_mulai);
            $selesaiLama = $sesi->jam_selesai ? Carbon::parse($sesi->jam_selesai)->addMinutes(15) : $mulaiLama;

            if ($mulaiBaru->lt($selesaiLama) && $selesaiBaru->gt($mulaiLama)) {
                return true;
            }
        }

        return false;
    }

    private function checkDateAvailability(Carbon $date, $category, $package, bool $butuhRuangan): array
    {
        $tanggal = $date->toDateString();

        // 0. Tanggal yang sudah lewat otomatis tidak bisa dipilih
        if ($date->startOfDay()->lt(now()->startOfDay())) {
            return ['tanggal' => $tanggal, 'available' => false, 'alasan' => 'sudah_lewat'];
        }

        // Wedding Pack DIKECUALIKAN dari pengecekan hari tutup — studio tutup hari
        // Minggu justru karena dipakai untuk acara Wedding, jadi Wedding sendiri
        // harus tetap bisa dibooking di hari Minggu.
        $isWeddingCategory = strtolower(trim($category->nama_kategori)) === 'wedding pack';

        // 1. Cek hari buka/tutup (dilewati khusus untuk Wedding Pack)
        if (! $isWeddingCategory) {
            $operatingHour = OperatingHour::where('hari', $date->dayOfWeek)->first();
            if (! $operatingHour || $operatingHour->is_tutup) {
                return ['tanggal' => $tanggal, 'available' => false, 'alasan' => 'tutup'];
            }
        } else {
            $operatingHour = OperatingHour::where('hari', $date->dayOfWeek)->first();
        }

        // 2. Cek block manual admin
        $isBlocked = ManualBlock::where('tanggal', $tanggal)->exists();
        if ($isBlocked) {
            return ['tanggal' => $tanggal, 'available' => false, 'alasan' => 'diblokir_admin'];
        }

        // 3. Cek kuota harian kategori (kalau null berarti unlimited, misal Pass Foto)
        if ($category->kuota_harian !== null) {
            $jumlahBooking = BookingSession::whereDate('tanggal', $tanggal)
                ->whereHas('booking', function ($q) use ($category) {
                    $q->where('status', '!=', 'dibatalkan')
                        ->whereHas('package', fn ($q2) => $q2->where('category_id', $category->id));
                })
                ->count();

            if ($jumlahBooking >= $category->kuota_harian) {
                return ['tanggal' => $tanggal, 'available' => false, 'alasan' => 'kuota_penuh'];
            }
        }

        // 4. Cek sisa kapasitas waktu ruangan (khusus kategori/paket yang butuh ruangan)
        if ($butuhRuangan) {
            $menitDibutuhkan = ($package->durasi_menit ?? 0) + 15;

            $menitTerpakai = BookingSession::whereDate('tanggal', $tanggal)
                ->whereHas('booking', function ($q) {
                    $q->where('status', '!=', 'dibatalkan')
                        ->whereHas('package.category', fn ($q2) => $q2->where('butuh_ruangan', true));
                })
                ->get()
                ->sum(function ($session) {
                    if (! $session->jam_selesai) {
                        return 0;
                    }

                    return Carbon::parse($session->jam_mulai)->diffInMinutes(Carbon::parse($session->jam_selesai));
                });

            $menitOperasional = Carbon::parse($operatingHour->jam_buka)
                ->diffInMinutes(Carbon::parse($operatingHour->jam_tutup));

            $sisaMenit = $menitOperasional - $menitTerpakai;

            if ($sisaMenit < $menitDibutuhkan) {
                return ['tanggal' => $tanggal, 'available' => false, 'alasan' => 'ruangan_penuh'];
            }
        }

        return ['tanggal' => $tanggal, 'available' => true, 'alasan' => null];
    }
}