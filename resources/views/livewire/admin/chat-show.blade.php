<div class="flex flex-col" style="height:calc(100vh - 120px)">

    @php
        $sesi        = $booking->sessions->first();
        $warnaStatus = match($booking->status) {
            'confirmed','hasil_siap'                           => 'bg-green-100 text-green-700 border-green-200',
            'menunggu_konfirmasi','menunggu_pembayaran_dp'     => 'bg-orange-100 text-orange-700 border-orange-200',
            'dibatalkan'                                       => 'bg-red-100 text-red-700 border-red-200',
            'selesai'                                          => 'bg-gray-100 text-gray-600 border-gray-200',
            default                                            => 'bg-blue-100 text-blue-700 border-blue-200',
        };
        $jamMulai    = $sesi ? substr($sesi->jam_mulai, 0, 5) : null;
        $durasi      = $booking->package->durasi_menit;
        $jamSelesai  = ($jamMulai && $durasi)
            ? \Carbon\Carbon::createFromFormat('H:i', $jamMulai)->addMinutes($durasi)->format('H:i')
            : null;
    @endphp

    {{-- ── Baris 1: Kembali ─────────────────────────────── --}}
    <div class="flex-shrink-0 mb-3">
        <a href="{{ route('admin.chat.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Chat
        </a>
    </div>

    {{-- ── Baris 2: Info Booking ─────────────────────────── --}}
    <div class="flex-shrink-0 bg-white rounded-2xl border border-gray-200 shadow-sm mb-3 overflow-hidden">
        <div class="flex items-stretch">

            {{-- Garis aksen kiri --}}
            <div class="w-1 flex-shrink-0
                {{ str_contains($warnaStatus,'green') ? 'bg-green-500' :
                   (str_contains($warnaStatus,'orange') ? 'bg-orange-400' :
                   (str_contains($warnaStatus,'red') ? 'bg-red-500' : 'bg-gray-400')) }}">
            </div>

            {{-- Info --}}
            <div class="flex-1 px-5 py-4 flex items-center justify-between gap-6 min-w-0">

                {{-- Kiri: semua info tersusun ke bawah --}}
                <div class="flex-1 min-w-0 space-y-2">

                    {{-- Nama + Status --}}
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-bold text-gray-900">{{ $booking->user->nama }}</p>
                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-lg border whitespace-nowrap {{ $warnaStatus }}">
                            {{ str_replace('_', ' ', ucfirst($booking->status)) }}
                        </span>
                    </div>

                    {{-- Kategori & Paket --}}
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3 h-3 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span class="text-xs text-indigo-600 font-medium">{{ $booking->package->category->nama_kategori ?? '-' }}</span>
                        <span class="text-gray-300 text-xs">·</span>
                        <span class="text-xs text-gray-700">{{ $booking->package->nama_paket }}</span>
                    </div>

                    {{-- Tanggal & Jam Sesi --}}
                    @if($sesi)
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($sesi->tanggal)->translatedFormat('d M Y') }}</span>
                        <span class="text-gray-300 text-xs">·</span>
                        <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-xs text-gray-600">{{ $jamMulai }}{{ $jamSelesai ? ' – '.$jamSelesai : '' }}</span>
                    </div>
                    @endif

                    {{-- No HP --}}
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span class="text-xs text-gray-500">{{ $booking->user->no_hp ?? '-' }}</span>
                    </div>
                </div>

                {{-- Kanan: ID + tombol --}}
                <div class="flex-shrink-0 flex flex-col items-end gap-2">
                    <span class="text-[10px] text-gray-400 font-mono">#{{ $booking->id }}</span>
                    <a href="{{ route('admin.bookings.show', $booking->id) }}"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold border border-gray-300 rounded-lg px-3 py-1.5 text-gray-700 hover:bg-gray-50 transition-colors whitespace-nowrap">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Detail Booking
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Baris 3: Room Chat ────────────────────────────── --}}
    <div class="flex-1 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col min-h-0">

        {{-- Bubble chat --}}
        <div class="flex-1 overflow-y-auto px-6 pt-6 pb-4 space-y-5 min-h-0"
             id="chat-messages"
             style="-ms-overflow-style:none;scrollbar-width:none"
             onscroll="">
            @forelse($booking->chatMessages->sortBy('created_at') as $pesan)
                <div class="flex {{ $pesan->sender_type === 'admin' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[58%]">
                        <p class="text-[11px] font-medium mb-1.5 {{ $pesan->sender_type === 'admin' ? 'text-right text-gray-400 mr-1' : 'text-gray-400 ml-1' }}">
                            {{ $pesan->sender_type === 'admin' ? 'Admin Studio' : $booking->user->nama }}
                        </p>
                        <div class="px-4 py-3 rounded-2xl text-sm leading-relaxed
                            {{ $pesan->sender_type === 'admin'
                                ? 'bg-gray-900 text-white rounded-tr-sm'
                                : 'bg-gray-100 text-gray-800 rounded-tl-sm' }}">
                            {{ $pesan->pesan }}
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1.5 {{ $pesan->sender_type === 'admin' ? 'text-right mr-1' : 'ml-1' }}">
                            {{ $pesan->created_at->translatedFormat('d M Y, H:i') }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center h-full py-20 text-center">
                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-600">Belum ada percakapan</p>
                    <p class="text-xs text-gray-400 mt-1">Mulai percakapan dengan pelanggan.</p>
                </div>
            @endforelse
        </div>

        {{-- Input --}}
        <div class="flex-shrink-0 px-6 py-4 border-t border-gray-100 bg-gray-50/40">
            <div class="flex gap-3 items-end">
                <textarea
                    wire:model="pesanBaru"
                    placeholder="Tulis pesan... (Enter kirim, Shift+Enter baris baru)"
                    rows="1"
                    id="chat-input"
                    class="flex-1 border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 bg-white resize-none overflow-hidden"
                    style="min-height:46px;max-height:120px"
                    oninput="this.style.height='auto';this.style.height=Math.min(this.scrollHeight,120)+'px'"
                    onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();@this.call('sendMessage')}"></textarea>
                <button wire:click="sendMessage"
                    class="bg-gray-900 text-white px-5 py-3 rounded-xl hover:bg-gray-800 transition-colors flex items-center gap-2 text-sm font-medium flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Kirim
                </button>
            </div>
            <p class="text-[10px] text-gray-400 mt-2 ml-1">Enter untuk kirim · Shift+Enter untuk baris baru</p>
        </div>
    </div>

    <script>
        (function () {
            function scrollToBottom() {
                const el = document.getElementById('chat-messages');
                if (el) el.scrollTop = el.scrollHeight;
            }
            // Sembunyikan scrollbar webkit
            const style = document.createElement('style');
            style.textContent = '#chat-messages::-webkit-scrollbar{display:none}';
            document.head.appendChild(style);

            scrollToBottom();
            document.addEventListener('livewire:updated', function () {
                scrollToBottom();
                const ta = document.getElementById('chat-input');
                if (ta) ta.style.height = '46px';
            });
        })();
    </script>

</div>