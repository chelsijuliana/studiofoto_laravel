<div>
    {{-- Kembali + Chat Pelanggan --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.bookings.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Manajemen Booking
        </a>
        <a href="{{ route('admin.chat.show', $booking->id) }}"
           class="inline-flex items-center gap-2 bg-gray-900 text-white text-sm px-4 py-2 rounded-xl hover:bg-gray-800 transition-colors font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            Chat Pelanggan
        </a>
    </div>

    {{-- Flash --}}
    @if(session('message'))
        <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-5">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('message') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-5">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    @php
        $warnaHeader = match($booking->status) {
            'confirmed', 'hasil_siap'                       => ['bg' => 'bg-green-600',  'badge' => 'bg-green-100 text-green-700 border-green-200'],
            'menunggu_konfirmasi', 'menunggu_pembayaran_dp' => ['bg' => 'bg-orange-500', 'badge' => 'bg-orange-100 text-orange-700 border-orange-200'],
            'dibatalkan'                                    => ['bg' => 'bg-red-600',    'badge' => 'bg-red-100 text-red-700 border-red-200'],
            'selesai'                                       => ['bg' => 'bg-gray-600',   'badge' => 'bg-gray-100 text-gray-600 border-gray-200'],
            default                                         => ['bg' => 'bg-blue-600',   'badge' => 'bg-blue-100 text-blue-700 border-blue-200'],
        };
        $sudahBayarDp = $booking->payments->where('jenis','dp')->where('status','success')->count() > 0;
    @endphp

    {{-- ── 1. HEADER BANNER ──────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-5">
        <div class="{{ $warnaHeader['bg'] }} px-6 py-5 flex items-center justify-between">
            <div>
                <p class="text-white/70 text-xs font-medium uppercase tracking-widest mb-1">Booking #{{ $booking->id }}</p>
                <p class="text-white font-bold text-xl">{{ $booking->package->nama_paket }}</p>
                <p class="text-white/80 text-sm mt-0.5">{{ $booking->package->category->nama_kategori ?? '-' }}</p>
            </div>
            <span class="text-xs font-semibold border {{ $warnaHeader['badge'] }} bg-white/90 px-3 py-1.5 rounded-full">
                {{ str_replace('_', ' ', ucfirst($booking->status)) }}
            </span>
        </div>
        {{-- Info pelanggan 4 kolom --}}
        <div class="grid grid-cols-4 divide-x divide-gray-100 bg-gray-50/60">
            <div class="px-5 py-4">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Pelanggan</p>
                <p class="text-sm font-semibold text-gray-900">{{ $booking->user->nama }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $booking->user->email }}</p>
            </div>
            <div class="px-5 py-4">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">No. HP</p>
                <p class="text-sm font-semibold text-gray-900">{{ $booking->user->no_hp ?? '-' }}</p>
            </div>
            <div class="px-5 py-4">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Total Harga</p>
                <p class="text-sm font-semibold text-gray-900">Rp{{ number_format($booking->total_harga, 0, ',', '.') }}</p>
            </div>
            <div class="px-5 py-4">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Sisa Pelunasan</p>
                @if($booking->sisaPelunasan() > 0)
                    <p class="text-sm font-bold text-red-600">Rp{{ number_format($booking->sisaPelunasan(), 0, ',', '.') }}</p>
                @else
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-50 border border-green-200 px-2.5 py-1 rounded-full">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Lunas
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- ── 2. DETAIL SESI FOTO ────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-5">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-sm font-semibold text-gray-800">Detail Sesi Foto</p>
        </div>
        @foreach($booking->sessions as $sesi)
            @php
                $jamMulai   = substr($sesi->jam_mulai, 0, 5);
                $durasi     = $booking->package->durasi_menit;
                $jamSelesai = $durasi
                    ? \Carbon\Carbon::createFromFormat('H:i', $jamMulai)->addMinutes($durasi)->format('H:i')
                    : null;
                $isWedding = in_array($sesi->jenis_sesi, ['akad', 'pesta']);
            @endphp
            <div class="px-6 py-5 grid grid-cols-{{ $isWedding ? '4' : '3' }} gap-6 {{ !$loop->first ? 'border-t border-gray-100' : '' }}">
                @if($isWedding)
                <div>
                    <p class="text-xs text-gray-400 mb-1.5">Jenis Sesi</p>
                    <p class="text-sm font-semibold text-gray-900">{{ ucfirst($sesi->jenis_sesi) }}</p>
                </div>
                @endif
                <div>
                    <p class="text-xs text-gray-400 mb-1.5">Tanggal</p>
                    <p class="text-sm font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($sesi->tanggal)->translatedFormat('l, d M Y') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1.5">Jam Sesi</p>
                    <p class="text-sm font-semibold text-gray-900">
                        {{ $jamMulai }}{{ $jamSelesai ? ' – ' . $jamSelesai : '' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1.5">Lokasi</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $sesi->alamat ?: 'Studio' }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── 3. RINCIAN HARGA ───────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-5">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
            <p class="text-sm font-semibold text-gray-800">Rincian Harga</p>
        </div>
        <div class="px-6 divide-y divide-gray-50">
            @if($booking->items->count())
                @foreach($booking->items as $item)
                    <div class="flex justify-between items-center py-3.5">
                        <span class="text-sm text-gray-600">
                            {{ $item->keterangan }}
                            @if($item->qty > 1)<span class="text-gray-400 text-xs ml-1">x{{ $item->qty }}</span>@endif
                        </span>
                        <span class="text-sm text-gray-800">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            @endif
            <div class="flex justify-between items-center py-3.5">
                <span class="text-sm text-gray-600">Total Harga</span>
                <span class="text-sm font-bold text-gray-900">Rp{{ number_format($booking->total_harga, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center py-3.5">
                <span class="text-sm text-gray-600">Sudah Dibayar</span>
                <span class="text-sm font-semibold text-green-600">Rp{{ number_format($booking->totalDibayar(), 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center py-3.5">
                <span class="text-sm text-gray-600">Sisa Pelunasan</span>
                @if($booking->sisaPelunasan() > 0)
                    <span class="text-sm font-bold text-red-600">Rp{{ number_format($booking->sisaPelunasan(), 0, ',', '.') }}</span>
                @else
                    <span class="text-xs font-semibold text-green-700 bg-green-50 border border-green-200 px-2.5 py-1 rounded-full">✓ Lunas</span>
                @endif
            </div>
        </div>
    </div>

    {{-- ── 4. RIWAYAT PEMBAYARAN ──────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-5">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            <p class="text-sm font-semibold text-gray-800">Riwayat Pembayaran</p>
        </div>

        {{-- Bukti transfer manual yang menunggu diverifikasi admin --}}
        @php
            $pembayaranMenungguVerifikasi = $booking->payments
                ->where('status', 'pending')
                ->where('metode_pembayaran', 'transfer_manual');
        @endphp
        @if($pembayaranMenungguVerifikasi->count())
            <div class="px-6 pt-5 space-y-4">
                @foreach($pembayaranMenungguVerifikasi as $payment)
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-2xl">
                        <div class="flex items-start justify-between gap-4 mb-3">
                            <div>
                                <p class="text-sm font-semibold text-yellow-800 capitalize">
                                    Transfer {{ $payment->jenis }} — menunggu verifikasi
                                </p>
                                <p class="text-xs text-yellow-700 mt-0.5">Rp{{ number_format($payment->jumlah, 0, ',', '.') }}</p>
                            </div>
                            @if($payment->bukti_transfer)
                                <a href="{{ $payment->bukti_transfer }}" target="_blank"
                                   class="shrink-0 w-16 h-16 rounded-lg border border-yellow-300 overflow-hidden bg-white">
                                    <img src="{{ $payment->bukti_transfer }}" class="w-full h-full object-cover">
                                </a>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="verifikasiPembayaran({{ $payment->id }})" wire:confirm="Setujui bukti transfer ini? Pembayaran akan tercatat berhasil."
                                class="flex-1 bg-green-600 text-white text-xs font-semibold px-4 py-2 rounded-xl hover:bg-green-700 transition-colors">
                                ✓ Verifikasi
                            </button>
                            <button wire:click="tolakPembayaran({{ $payment->id }})" wire:confirm="Tolak bukti transfer ini? Pelanggan akan diminta upload ulang."
                                class="flex-1 bg-white border border-red-300 text-red-600 text-xs font-semibold px-4 py-2 rounded-xl hover:bg-red-50 transition-colors">
                                ✕ Tolak
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @php
            // List di bawah ini gak perlu nampilin lagi pembayaran yang sudah
            // ditampilkan di kartu "menunggu verifikasi" di atas, biar gak dobel.
            $riwayatPembayaranLain = $booking->payments->reject(
                fn ($p) => $pembayaranMenungguVerifikasi->contains('id', $p->id)
            );
        @endphp
        <div class="px-6 divide-y divide-gray-50">
            @forelse($riwayatPembayaranLain as $payment)
                <div class="flex items-center justify-between py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center
                            {{ $payment->status === 'success' ? 'bg-green-50' : ($payment->status === 'failed' ? 'bg-red-50' : 'bg-yellow-50') }}">
                            <svg class="w-4 h-4 {{ $payment->status === 'success' ? 'text-green-600' : ($payment->status === 'failed' ? 'text-red-500' : 'text-yellow-500') }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 capitalize">{{ $payment->jenis }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, H:i') : 'Belum dibayar' }}
                                @if($payment->metode) · {{ $payment->metode }} @endif
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">Rp{{ number_format($payment->jumlah, 0, ',', '.') }}</p>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full
                            {{ $payment->status === 'success' ? 'bg-green-50 text-green-700' : ($payment->status === 'failed' ? 'bg-red-50 text-red-600' : 'bg-yellow-50 text-yellow-700') }}">
                            {{ $payment->status }}
                        </span>
                    </div>
                </div>
            @empty
                @if($pembayaranMenungguVerifikasi->isEmpty())
                    <p class="text-sm text-gray-400 py-6 text-center">Belum ada pembayaran.</p>
                @endif
            @endforelse
        </div>

        {{-- Catat pelunasan cash --}}
        @if($booking->sisaPelunasan() > 0)
            <div class="mx-6 mb-6 mt-4 p-5 bg-gray-50 border border-gray-200 rounded-2xl">
                <p class="text-xs font-semibold text-gray-700 mb-3">Catat Pelunasan Cash (bayar langsung di studio)</p>
                <div class="flex gap-2">
                    <input type="number" wire:model="jumlahPelunasan"
                        placeholder="Nominal: {{ number_format($booking->sisaPelunasan(), 0, ',', '.') }}"
                        class="flex-1 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    <button wire:click="catatPelunasanCash"
                        class="bg-gray-900 text-white text-sm px-5 py-2.5 rounded-xl hover:bg-gray-800 transition-colors whitespace-nowrap font-medium">
                        Catat
                    </button>
                </div>
                @error('jumlahPelunasan')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
            </div>
        @endif
    </div>

    {{-- ── 5. KONFIRMASI BIAYA LUAR KOTA ─────────────────────────── --}}
    @if($booking->status === 'menunggu_konfirmasi')
        <div class="bg-orange-50 border border-orange-200 rounded-2xl px-6 py-5 mb-5">
            <p class="text-sm font-semibold text-orange-800 mb-3">⚠️ Konfirmasi Biaya Luar Kota</p>
            <div class="flex gap-2">
                <input type="number" wire:model="biayaTambahan" placeholder="contoh: 500000"
                    class="flex-1 border border-orange-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">
                <button wire:click="confirmPrice"
                    class="bg-orange-500 text-white text-sm px-5 py-2.5 rounded-xl hover:bg-orange-600 transition-colors whitespace-nowrap font-medium">
                    Konfirmasi
                </button>
            </div>
            @error('biayaTambahan')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
        </div>
    @endif

    {{-- ── 6. UBAH STATUS ─────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-5">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <p class="text-sm font-semibold text-gray-800">Ubah Status Booking</p>
        </div>
        <div class="px-6 pt-5 pb-6">
            <div class="flex gap-3 items-start">
                <select wire:model="status"
                    class="flex-1 border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                    <option value="menunggu_konfirmasi">Menunggu Konfirmasi</option>
                    <option value="menunggu_pembayaran_dp">Menunggu Pembayaran DP</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="sedang_diproses">Sedang Diproses</option>
                    <option value="hasil_siap">Hasil Siap</option>
                    <option value="selesai">Selesai</option>
                    @if(!$sudahBayarDp)
                        <option value="dibatalkan">Dibatalkan</option>
                    @else
                        <option value="dibatalkan" disabled class="text-gray-400">Dibatalkan (ada DP)</option>
                    @endif
                </select>
                <button wire:click="$dispatch('konfirmasi-status')"
                    class="bg-gray-900 text-white text-sm px-6 py-2.5 rounded-xl hover:bg-gray-800 transition-colors whitespace-nowrap font-medium">
                    Simpan Status
                </button>
            </div>

            @if($sudahBayarDp)
                <p class="text-xs text-red-500 bg-red-50 border border-red-100 rounded-xl px-4 py-2.5 mt-3">
                    ⚠️ Pembatalan tidak dapat dilakukan karena pelanggan sudah membayar DP.
                </p>
            @endif

            <div class="mt-4 pt-4 border-t border-gray-100">
                <label class="flex items-center gap-3 text-sm text-gray-700 cursor-pointer mb-1">
                    <input type="checkbox" wire:click="togglePickedUp" @checked($booking->status_pengambilan_fisik)
                        class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                    <span>Hasil cetak fisik sudah diambil pelanggan</span>
                </label>
            </div>
        </div>
    </div>

    {{-- ── 7. HASIL FOTO & VIDEO ──────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-5">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-sm font-semibold text-gray-800">Hasil Foto & Video</p>
        </div>
        <div class="px-6 pt-5 pb-6 space-y-4">
            <div x-data="{ previews: [] }">
                <label class="text-xs text-gray-500 font-medium block mb-2">Upload Foto</label>
                <input type="file" wire:model="photos" multiple
                    x-on:change="
                        previews = [];
                        for (const file of $event.target.files) {
                            previews.push(URL.createObjectURL(file));
                        }
                    "
                    class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border file:border-gray-300 file:text-xs file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">

                {{-- Preview foto yang baru dipilih (langsung dari browser, gak nunggu upload ke server) --}}
                <div wire:loading wire:target="photos" class="text-xs text-gray-400 mt-2">Mengunggah ke server...</div>
                <div class="flex flex-wrap gap-2 mt-3" x-show="previews.length > 0">
                    <template x-for="(src, index) in previews" :key="index">
                        <div class="h-24 w-24 shrink-0 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center overflow-hidden">
                            <img :src="src" class="max-h-full max-w-full object-contain">
                        </div>
                    </template>
                </div>
            </div>
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-2">Link Video (Google Drive / YouTube)</label>
                <input type="text" wire:model="videoLink" placeholder="https://drive.google.com/..."
                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
            </div>
            <button wire:click="uploadFiles"
                class="w-full bg-gray-900 text-white text-sm py-2.5 rounded-xl hover:bg-gray-800 transition-colors font-medium">
                Upload
            </button>

            @if($booking->files->count())
                <div class="flex flex-wrap gap-2 pt-2">
                    @foreach($booking->files as $file)
                        <div class="relative group h-24 w-24 shrink-0">
                            @if($file->file_type === 'foto')
                                <div class="h-24 w-24 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center overflow-hidden">
                                    <img src="{{ $file->file_url }}" class="max-h-full max-w-full object-contain">
                                </div>
                            @else
                                <a href="{{ $file->file_url }}" target="_blank"
                                   class="flex flex-col items-center justify-center h-24 w-24 bg-blue-50 rounded-lg border border-blue-200 text-[10px] text-blue-600 font-medium gap-0.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Video
                                </a>
                            @endif
                            <button wire:click="hapusFile({{ $file->id }})" wire:confirm="Hapus file ini?"
                                class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white rounded-full text-[10px] flex items-center justify-center shadow opacity-0 group-hover:opacity-100 transition-opacity">
                                ✕
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── MODAL KONFIRMASI STATUS ─────────────────────────────────── --}}
    <div
        x-data="{ terbuka: false, statusBaru: '' }"
        x-on:konfirmasi-status.window="terbuka = true; statusBaru = $wire.status"
        x-show="terbuka"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
        style="display:none">
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm mx-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-11 h-11 rounded-full bg-yellow-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-sm">Konfirmasi Perubahan Status</p>
                    <p class="text-xs text-gray-500">Pastikan status yang dipilih sudah benar.</p>
                </div>
            </div>
            <p class="text-sm text-gray-700 mb-5">
                Ubah status booking <strong class="text-gray-900">{{ $booking->package->nama_paket }}</strong>
                menjadi <strong x-text="statusBaru.replaceAll('_',' ')" class="text-gray-900"></strong>?
            </p>
            <div class="flex border-t border-gray-100">
                <button x-on:click="terbuka = false"
                    class="flex-1 py-3 text-sm text-gray-700 hover:bg-gray-50 font-medium rounded-bl-2xl border-r border-gray-100 transition-colors">
                    Batal
                </button>
                <button x-on:click="terbuka = false; $wire.updateStatus()"
                    class="flex-1 py-3 text-sm font-bold text-gray-900 hover:bg-gray-50 rounded-br-2xl transition-colors">
                    Ya, Simpan
                </button>
            </div>
        </div>
    </div>
</div>