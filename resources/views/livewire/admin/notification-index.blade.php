<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Semua Notifikasi</h1>
            <p class="text-sm text-gray-500 mt-0.5">Riwayat lengkap notifikasi, tidak dibatasi jumlah.</p>
        </div>
        @if($jumlahBelumDibaca > 0)
            <button wire:click="tandaiSemuaDibaca" class="text-xs font-semibold text-[#C9A84C] hover:text-[#a98a3a] transition-colors bg-white border border-gray-200 rounded-lg px-3 py-2 shadow-sm">
                Tandai semua dibaca ({{ $jumlahBelumDibaca }})
            </button>
        @endif
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @forelse($notifications as $notif)
            @php
                // Ikon + warna per jenis notifikasi, sama persis dengan yang
                // dipakai di dropdown lonceng (notification-bell.blade.php).
                $tampilan = match($notif->type) {
                    'chat_baru' => [
                        'bg' => 'bg-blue-50', 'text' => 'text-blue-600',
                        'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
                    ],
                    'booking_baru', 'booking_terkirim' => [
                        'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600',
                        'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                    ],
                    'pembayaran_masuk', 'pembayaran_berhasil' => [
                        'bg' => 'bg-amber-50', 'text' => 'text-amber-600',
                        'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z',
                    ],
                    'booking_dibatalkan_otomatis' => [
                        'bg' => 'bg-red-50', 'text' => 'text-red-600',
                        'icon' => 'M6 18L18 6M6 6l12 12',
                    ],
                    default => [
                        'bg' => 'bg-gray-100', 'text' => 'text-gray-500',
                        'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                    ],
                };
            @endphp
            <button wire:click="bacaNotifikasi({{ $notif->id }})"
                class="w-full text-left px-5 py-4 hover:bg-gray-50/80 transition-colors flex items-start gap-3 cursor-pointer border-b border-gray-50 last:border-0 {{ $notif->is_read ? 'opacity-60' : 'bg-amber-50/30' }}">
                <div class="w-10 h-10 rounded-full {{ $tampilan['bg'] }} {{ $tampilan['text'] }} flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tampilan['icon'] }}"></path></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1.5">
                        <p class="text-sm font-semibold text-gray-900 leading-snug">{{ $notif->judul }}</p>
                        @if(! $notif->is_read)
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 shrink-0"></span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-600 leading-relaxed mt-0.5">{{ $notif->pesan }}</p>
                    <p class="text-[10px] font-medium text-gray-400 mt-1.5">
                        {{ $notif->created_at->translatedFormat('d M Y, H:i') }} &middot; {{ $notif->created_at->diffForHumans() }}
                    </p>
                </div>
            </button>
        @empty
            <div class="p-10 flex flex-col items-center justify-center text-center">
                <svg class="w-9 h-9 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <p class="text-sm text-gray-400">Belum ada notifikasi.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="mt-5">
            {{ $notifications->links() }}
        </div>
    @endif
</div>