<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use App\Models\ServiceCategory;
use Livewire\Component;
use Livewire\WithPagination;

class BookingIndex extends Component
{
    use WithPagination;

    public string $filterStatus = 'semua';
    public string $filterKategori = 'semua';
    public string $filterTanggal = '';
    public string $search = '';

    protected $queryString = ['filterStatus', 'filterKategori', 'filterTanggal', 'search'];

    public function setFilter(string $status)
    {
        $this->filterStatus = $status;
        $this->resetPage();
    }

    public function updated($property)
    {
        if (in_array($property, ['filterKategori', 'filterTanggal', 'search'])) {
            $this->resetPage();
        }
    }

    public function getStatusLabelsProperty(): array
    {
        return [
            'semua'                  => 'Semua',
            'menunggu_konfirmasi'    => 'Menunggu Konfirmasi',
            'menunggu_pembayaran_dp' => 'Menunggu Pembayaran DP',
            'confirmed'              => 'Dikonfirmasi',
            'sedang_diproses'        => 'Sedang Diproses',
            'hasil_siap'             => 'Hasil Siap',
            'selesai'                => 'Selesai',
            'dibatalkan'             => 'Dibatalkan',
        ];
    }

    public function render()
    {
        $query = Booking::with('user', 'package.category', 'sessions')->latest();

        if ($this->filterStatus !== 'semua') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterKategori !== 'semua') {
            $query->whereHas('package', fn ($q) => $q->where('category_id', $this->filterKategori));
        }

        if ($this->filterTanggal) {
            $query->whereHas('sessions', fn ($q) => $q->whereDate('tanggal', $this->filterTanggal));
        }

        if ($this->search) {
            $query->whereHas('user', fn ($q) => $q->where('nama', 'like', '%'.$this->search.'%'));
        }

        $bookings = $query->paginate(10);

        return view('livewire.admin.booking-index', [
            'bookings' => $bookings,
            'kategoriList' => ServiceCategory::all(),
        ]);
    }
}