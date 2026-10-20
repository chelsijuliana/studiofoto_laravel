<div>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Booking</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar seluruh pesanan dan reservasi sesi foto pelanggan.</p>
        </div>
        <div class="relative w-full md:w-72">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Cari nama pelanggan..." class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900 shadow-sm">
        </div>
    </div>

    <!-- Filters Row -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6 space-y-3">
        <div class="flex flex-wrap items-center gap-3">
            <select wire:model.live="filterKategori" class="bg-white border border-gray-300 rounded-lg px-3 py-1.5 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
                <option value="semua">Semua Kategori</option>
                @foreach($kategoriList as $kat)
                    <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                @endforeach
            </select>
            <input type="date" wire:model.live="filterTanggal" class="bg-white border border-gray-300 rounded-lg px-3 py-1.5 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
            @if($filterTanggal)
                <button wire:click="$set('filterTanggal', '')" class="text-xs font-medium text-red-600 hover:text-red-800 transition-colors cursor-pointer">Hapus tanggal</button>
            @endif
        </div>

        <div class="flex gap-2 flex-wrap pt-2 border-t border-gray-100">
            @foreach($this->statusLabels as $key => $label)
                <button wire:click="setFilter('{{ $key }}')" class="text-xs px-3 py-1.5 rounded-lg font-medium transition-all cursor-pointer border {{ $filterStatus === $key ? 'bg-gray-900 text-white border-gray-900 shadow-xs' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-200 text-xs font-semibold uppercase tracking-wider text-gray-500">
                        <th class="py-3.5 px-4">Pelanggan</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4">Paket</th>
                        <th class="py-3.5 px-4">Tanggal Sesi</th>
                        <th class="py-3.5 px-4">Jam Sesi</th>
                        <th class="py-3.5 px-4">Total</th>
                        <th class="py-3.5 px-4">Sisa Pelunasan</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $booking->user->nama }}</td>
                            <td class="py-3.5 px-4">
                                <span class="text-sm text-gray-800">
                                    {{ $booking->package->category->nama_kategori ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-gray-700">{{ $booking->package->nama_paket }}</td>
                            <td class="py-3.5 px-4 text-gray-600 text-sm">
                                @php $sesiPertama = $booking->sessions->first(); @endphp
                                @if($sesiPertama)
                                    <div class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($sesiPertama->tanggal)->translatedFormat('d M Y') }}</div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-sm">
                                @if($sesiPertama && $sesiPertama->jam_mulai)
                                    @php
                                        $jamMulai  = substr($sesiPertama->jam_mulai, 0, 5);
                                        $durasi    = $booking->package->durasi_menit;
                                        if ($durasi && $durasi > 0) {
                                            $jamSelesai = \Carbon\Carbon::createFromFormat('H:i', $jamMulai)
                                                            ->addMinutes($durasi)
                                                            ->format('H:i');
                                            $jamTampil = $jamMulai . ' – ' . $jamSelesai;
                                        } else {
                                            $jamTampil = $jamMulai;
                                        }
                                    @endphp
                                    <span class="font-medium text-gray-800">{{ $jamTampil }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-medium text-gray-900">Rp{{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4">
                                @php $sisa = $booking->sisaPelunasan(); @endphp
                                @if($sisa > 0)
                                    <span class="font-semibold text-red-600">Rp{{ number_format($sisa, 0, ',', '.') }}</span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 bg-green-50 px-2.5 py-1 rounded-full border border-green-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $warnaBadge = match($booking->status) {
                                        'confirmed', 'hasil_siap' => 'bg-green-50 text-green-700 border-green-200',
                                        'menunggu_konfirmasi', 'menunggu_pembayaran_dp' => 'bg-orange-50 text-orange-700 border-orange-200',
                                        'dibatalkan' => 'bg-red-50 text-red-700 border-red-200',
                                        'selesai' => 'bg-gray-100 text-gray-600 border-gray-200',
                                        default => 'bg-blue-50 text-blue-700 border-blue-200',
                                    };
                                @endphp
                                <span class="text-xs font-medium border {{ $warnaBadge }} px-2.5 py-1 rounded-full">{{ $this->statusLabels[$booking->status] ?? $booking->status }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="text-xs font-medium border border-gray-300 rounded-lg px-3 py-1.5 text-gray-700 hover:bg-gray-50 transition-colors inline-block whitespace-nowrap">Lihat Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-400 text-sm italic">Belum ada booking.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $bookings->links() }}
    </div>
</div>