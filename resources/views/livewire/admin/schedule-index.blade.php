<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-semibold">Jadwal - {{ $awalBulan->translatedFormat('F Y') }}</h1>
        <div class="flex items-center gap-2">
            <select wire:model.live="bulan" class="border rounded px-2 py-1.5 text-sm">
                @foreach(range(1, 12) as $b)
                    <option value="{{ $b }}">{{ \Carbon\Carbon::create(2000, $b, 1)->translatedFormat('F') }}</option>
                @endforeach
            </select>
            <select wire:model.live="tahun" class="border rounded px-2 py-1.5 text-sm">
                @foreach(range(now()->year - 1, now()->year + 2) as $t)
                    <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
            </select>
            <button wire:click="gantiBulan(-1)" class="border rounded px-3 py-1.5 text-sm">&larr;</button>
            <button wire:click="gantiBulan(1)" class="border rounded px-3 py-1.5 text-sm">&rarr;</button>
        </div>
    </div>

    @if (session('message'))
        <div class="bg-green-50 text-green-700 text-sm px-3 py-2 rounded mb-4">{{ session('message') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg mb-4 flex items-start gap-2">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-7 gap-2 mb-8">
        @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $hari)
            <div class="text-xs text-gray-400 text-center">{{ $hari }}</div>
        @endforeach

        @php
            $awalMinggu = $awalBulan->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
            $akhirTampil = $awalBulan->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SUNDAY);
        @endphp

        @for($tgl = $awalMinggu->copy(); $tgl->lte($akhirTampil); $tgl->addDay())
            @php
                $tglStr = $tgl->toDateString();
                $sudahLewat = $tgl->lt(now()->startOfDay());
            @endphp
            <button wire:click="pilihTanggal('{{ $tglStr }}')"
                class="aspect-square rounded p-2 text-sm text-left
                    {{ $tgl->month !== $bulan ? 'opacity-30' : '' }}
                    {{ $tanggalDipilih === $tglStr ? 'border-2 border-gray-900' : 'bg-white' }}
                    {{ $tanggalDiblokir->contains($tglStr) ? 'bg-red-50 text-red-600' : '' }}">
                {{ $tgl->day }}
                @if($tanggalDenganBooking->contains($tglStr))
                    <div class="w-1.5 h-1.5 rounded-full {{ $sudahLewat ? 'bg-gray-400' : 'bg-blue-500' }} mt-1"></div>
                @endif
            </button>
        @endfor
    </div>

    <div class="flex justify-between items-center mb-3">
        <h2 class="text-base font-medium">Jadwal {{ \Carbon\Carbon::parse($tanggalDipilih)->translatedFormat('d F Y') }}</h2>
        @if($isDiblokir)
            <button wire:click="hapusBlock" class="text-xs border rounded px-3 py-1.5 text-red-600">Buka blokir</button>
        @else
            <button wire:click="bukaFormBlock" class="text-xs border rounded px-3 py-1.5">Blokir tanggal</button>
        @endif
    </div>

    <div class="space-y-2">
        @forelse($sesiHariIni as $sesi)
            <div class="bg-white rounded-lg p-3 flex justify-between items-center">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded-full">
                            {{ $sesi->booking->package->category->nama_kategori ?? '-' }}
                        </span>
                        @if($sesi->jenis_sesi && $sesi->jenis_sesi !== 'utama')
                            <span class="text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full capitalize">
                                Sesi {{ $sesi->jenis_sesi }}
                            </span>
                        @endif
                    </div>
                    <p class="text-sm">{{ $sesi->jam_mulai }} @if($sesi->jam_selesai) - {{ $sesi->jam_selesai }} @endif</p>
                    <p class="text-sm font-medium">{{ $sesi->booking->package->nama_paket }} - {{ $sesi->booking->user->nama }}</p>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400">Tidak ada jadwal di tanggal ini.</p>
        @endforelse
    </div>

    @if($showBlockForm)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50" wire:click.self="$set('showBlockForm', false)">
            <div class="bg-white rounded-lg p-6 w-full max-w-sm">
                <p class="font-medium mb-4">Blokir tanggal {{ \Carbon\Carbon::parse($tanggalDipilih)->format('d M Y') }}</p>
                <input type="text" wire:model="alasanBlock" placeholder="Alasan (contoh: renovasi)" class="w-full border rounded px-3 py-2 text-sm mb-4">
                <div class="flex gap-2">
                    <button wire:click="$set('showBlockForm', false)" class="flex-1 border rounded px-3 py-2 text-sm">Batal</button>
                    <button wire:click="simpanBlock" class="flex-1 bg-gray-900 text-white rounded px-3 py-2 text-sm">Simpan</button>
                </div>
            </div>
        </div>
    @endif
</div>