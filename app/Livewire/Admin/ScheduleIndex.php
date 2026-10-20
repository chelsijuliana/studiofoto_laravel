<?php

namespace App\Livewire\Admin;

use App\Models\BookingSession;
use App\Models\ManualBlock;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ScheduleIndex extends Component
{
    public int $bulan;
    public int $tahun;
    public ?string $tanggalDipilih = null;

    public bool $showBlockForm = false;
    public string $alasanBlock = '';

    public function mount()
    {
        $this->bulan = now()->month;
        $this->tahun = now()->year;
        $this->tanggalDipilih = now()->toDateString();
    }

    public function gantiBulan(int $arah)
    {
        $tanggal = Carbon::create($this->tahun, $this->bulan, 1)->addMonths($arah);
        $this->bulan = $tanggal->month;
        $this->tahun = $tanggal->year;
    }

    public function pilihTanggal(string $tanggal)
    {
        $this->tanggalDipilih = $tanggal;
    }

    public function bukaFormBlock()
    {
        $this->alasanBlock = '';
        $this->showBlockForm = true;
    }

    public function simpanBlock()
    {
        // Tidak boleh blokir tanggal yang masih punya jadwal booking aktif
        // (apapun kategorinya), karena blokir tanggal ini dipakai untuk
        // menutup SELURUH studio di tanggal itu (lihat pengecekan yang sama
        // di BookingController::checkDateAvailability).
        $adaBooking = BookingSession::whereDate('tanggal', $this->tanggalDipilih)
            ->whereHas('booking', fn ($q) => $q->where('status', '!=', 'dibatalkan'))
            ->exists();

        if ($adaBooking) {
            $this->showBlockForm = false;
            $tanggalFormatted = Carbon::parse($this->tanggalDipilih)->translatedFormat('d F Y');
            session()->flash('error', "Tanggal {$tanggalFormatted} tidak bisa diblokir karena sudah ada jadwal booking pada tanggal tersebut.");

            return;
        }

        ManualBlock::updateOrCreate(
            ['tanggal' => $this->tanggalDipilih],
            ['alasan' => $this->alasanBlock, 'admin_id' => Auth::id()]
        );

        $this->showBlockForm = false;
        session()->flash('message', 'Tanggal berhasil diblokir.');
    }

    public function hapusBlock()
    {
        ManualBlock::where('tanggal', $this->tanggalDipilih)->delete();
        session()->flash('message', 'Blokir tanggal dihapus.');
    }

    public function render()
    {
        $awalBulan = Carbon::create($this->tahun, $this->bulan, 1)->startOfMonth();
        $akhirBulan = $awalBulan->copy()->endOfMonth();

        $tanggalDenganBooking = BookingSession::whereBetween('tanggal', [$awalBulan, $akhirBulan])
            ->whereHas('booking', fn ($q) => $q->where('status', '!=', 'dibatalkan'))
            ->pluck('tanggal')
            ->map(fn ($t) => $t->toDateString())
            ->unique();

        $tanggalDiblokir = ManualBlock::whereBetween('tanggal', [$awalBulan, $akhirBulan])
            ->pluck('tanggal')
            ->map(fn ($t) => $t->toDateString());

        $sesiHariIni = BookingSession::with('booking.user', 'booking.package.category')
            ->whereDate('tanggal', $this->tanggalDipilih)
            ->whereHas('booking', fn ($q) => $q->where('status', '!=', 'dibatalkan'))
            ->orderBy('jam_mulai')
            ->get();

        $isDiblokir = ManualBlock::where('tanggal', $this->tanggalDipilih)->exists();

        return view('livewire.admin.schedule-index', [
            'awalBulan' => $awalBulan,
            'tanggalDenganBooking' => $tanggalDenganBooking,
            'tanggalDiblokir' => $tanggalDiblokir,
            'sesiHariIni' => $sesiHariIni,
            'isDiblokir' => $isDiblokir,
        ]);
    }
}