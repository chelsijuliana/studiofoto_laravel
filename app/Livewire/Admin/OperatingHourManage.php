<?php

namespace App\Livewire\Admin;

use App\Models\BookingSession;
use App\Models\OperatingHour;
use Carbon\Carbon;
use Livewire\Component;

class OperatingHourManage extends Component
{
    public array $hours = [];

    public array $namaHari = [
        0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu',
        4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu',
    ];

    public function mount()
    {
        $this->loadHours();
    }

    public function loadHours()
    {
        $data = OperatingHour::orderBy('hari')->get();

        $this->hours = $data->map(fn ($h) => [
            'id' => $h->id,
            'hari' => $h->hari,
            'jam_buka' => $h->jam_buka ? substr($h->jam_buka, 0, 5) : '10:00',
            'jam_tutup' => $h->jam_tutup ? substr($h->jam_tutup, 0, 5) : '20:00',
            'is_tutup' => (bool) $h->is_tutup,
        ])->toArray();
    }

    public function simpan()
    {
        $errors = [];

        foreach ($this->hours as $item) {
            $hariNama = $this->namaHari[$item['hari']] ?? "hari {$item['hari']}";

            if ($item['is_tutup']) {
                // 1. Tidak boleh set hari jadi TUTUP kalau masih ada booking
                // (yang butuh ruangan) di masa depan yang jatuh pada hari itu.
                if ($this->sesiAktifPadaHari($item['hari'])->isNotEmpty()) {
                    $errors[] = "Studio tidak bisa tutup pada hari {$hariNama} karena sudah ada jadwal booking.";
                }

                continue;
            }

            // 2. Kalau tetap buka, jam buka/tutup baru tidak boleh mempersempit
            // rentang sampai ada booking existing yang jadi di luar jam
            // operasional (baik dari sisi jam buka maupun jam tutup).
            $sesiBentrok = $this->sesiAktifPadaHari($item['hari'])->first(function ($sesi) use ($item) {
                $mulaiSesi = Carbon::parse($sesi->jam_mulai);
                $selesaiSesi = $sesi->jam_selesai ? Carbon::parse($sesi->jam_selesai) : $mulaiSesi;

                $jamBukaBaru = Carbon::parse($item['jam_buka']);
                $jamTutupBaru = Carbon::parse($item['jam_tutup']);

                return $mulaiSesi->lt($jamBukaBaru) || $selesaiSesi->gt($jamTutupBaru);
            });

            if ($sesiBentrok) {
                $jamSesi = Carbon::parse($sesiBentrok->jam_mulai)->format('H:i');
                $tanggalSesi = Carbon::parse($sesiBentrok->tanggal)->translatedFormat('d M Y');
                $errors[] = "Jam operasional hari {$hariNama} tidak bisa diubah karena ada booking pukul {$jamSesi} pada {$tanggalSesi} yang berada di luar rentang jam baru.";
            }
        }

        if (! empty($errors)) {
            session()->flash('error', implode(' ', $errors));

            return;
        }

        foreach ($this->hours as $item) {
            OperatingHour::where('id', $item['id'])->update([
                'jam_buka' => $item['is_tutup'] ? null : $item['jam_buka'],
                'jam_tutup' => $item['is_tutup'] ? null : $item['jam_tutup'],
                'is_tutup' => $item['is_tutup'],
            ]);
        }

        $this->loadHours();
        session()->flash('message', 'Jam operasional berhasil disimpan.');
    }

    // Ambil semua sesi booking aktif (bukan "dibatalkan") yang butuh ruangan,
    // untuk tanggal hari ini atau setelahnya, yang jatuh pada hari-dalam-minggu
    // tertentu ($hari: 0=Minggu ... 6=Sabtu, sama seperti kolom OperatingHour->hari).
    private function sesiAktifPadaHari(int $hari)
    {
        return BookingSession::whereDate('tanggal', '>=', now()->toDateString())
            ->whereHas('booking', function ($q) {
                $q->where('status', '!=', 'dibatalkan')
                    ->whereHas('package.category', fn ($q2) => $q2->where('butuh_ruangan', true));
            })
            ->get()
            ->filter(fn ($sesi) => Carbon::parse($sesi->tanggal)->dayOfWeek === $hari)
            ->sortBy('jam_mulai')
            ->values();
    }

    public function render()
    {
        return view('livewire.admin.operating-hour-manage');
    }
}