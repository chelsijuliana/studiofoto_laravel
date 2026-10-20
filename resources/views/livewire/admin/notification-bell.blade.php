<div class="relative">
    <!-- Bell Button -->
    <button wire:click="toggleDropdown" class="relative p-2.5 rounded-xl bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all shadow-2xs cursor-pointer flex items-center justify-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>

        @if($jumlahBelumDibaca > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center border-2 border-white shadow-2xs animate-pulse">
                {{ $jumlahBelumDibaca > 9 ? '9+' : $jumlahBelumDibaca }}
            </span>
        @endif
    </button>

    <!-- Notification Dropdown Menu -->
    @if($showDropdown)
        <div class="absolute right-0 mt-2.5 w-80 md:w-96 bg-white rounded-2xl shadow-xl border border-gray-200 z-50 max-h-[26rem] overflow-y-auto no-scrollbar">
            <!-- Header -->
            <div class="flex justify-between items-center px-4 py-3.5 border-b border-gray-100 sticky top-0 bg-white/95 backdrop-blur-xs z-10">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-gray-900">Notifikasi</span>
                    @if($jumlahBelumDibaca > 0)
                        <span class="bg-red-100 text-red-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                            {{ $jumlahBelumDibaca }} baru
                        </span>
                    @endif
                </div>
                @if($jumlahBelumDibaca > 0)
                    <button wire:click="tandaiSemuaDibaca" class="text-xs font-semibold text-[#C9A84C] hover:text-[#a98a3a] transition-colors cursor-pointer">
                        Tandai semua dibaca
                    </button>
                @endif
            </div>

            <!-- List -->
            <div class="divide-y divide-gray-50">
                @forelse($notifications as $notif)
                    @php
                        // Ikon + warna per jenis notifikasi, biar gampang dikenali sekilas
                        $tampilan = match($notif->type) {
                            'chat_baru' => [
                                'bg' => 'bg-blue-50', 'text' => 'text-blue-600',
                                'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
                            ],
                            'booking_baru' => [
                                'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600',
                                'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                            ],
                            'pembayaran_masuk', 'pembayaran_berhasil' => [
                                'bg' => 'bg-amber-50', 'text' => 'text-amber-600',
                                'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z',
                            ],
                            default => [
                                'bg' => 'bg-gray-100', 'text' => 'text-gray-500',
                                'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                            ],
                        };
                    @endphp
                    <button wire:click="bacaNotifikasi({{ $notif->id }})"
                        class="w-full text-left px-4 py-3.5 hover:bg-gray-50/80 transition-colors flex items-start gap-3 cursor-pointer {{ $notif->is_read ? 'opacity-60' : 'bg-amber-50/30' }}">
                        <div class="w-9 h-9 rounded-full {{ $tampilan['bg'] }} {{ $tampilan['text'] }} flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tampilan['icon'] }}"></path></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <p class="text-sm font-semibold text-gray-900 leading-snug truncate">{{ $notif->judul }}</p>
                                @if(! $notif->is_read)
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 shrink-0"></span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-600 leading-relaxed mt-0.5 line-clamp-2">{{ $notif->pesan }}</p>
                            <p class="text-[10px] font-medium text-gray-400 mt-1.5 flex items-center gap-1">
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ $notif->created_at->diffForHumans() }}</span>
                            </p>
                        </div>
                    </button>
                @empty
                    <div class="p-8 flex flex-col items-center justify-center text-center">
                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <p class="text-xs text-gray-400">Belum ada notifikasi saat ini.</p>
                    </div>
                @endforelse
            </div>
            <!-- Footer: link ke halaman semua notifikasi -->
            <a href="{{ route('admin.notifications.index') }}" class="block text-center px-4 py-3 border-t border-gray-100 text-xs font-semibold text-[#C9A84C] hover:text-[#a98a3a] hover:bg-gray-50/80 transition-colors">
                Lihat Semua Notifikasi
            </a>
        </div>
    @endif
</div>