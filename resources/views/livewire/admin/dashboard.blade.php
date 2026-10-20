<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Dashboard Overview</h1>
        <p class="text-sm text-gray-500 mt-1">Ringkasan aktivitas booking dan transaksi studio anda.</p>
    </div>

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Booking Hari Ini</p>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-3">{{ $bookingHariIni }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Perlu Direspon</p>
                <div class="p-2 bg-orange-50 text-orange-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-orange-600 mt-3">{{ $perluDirespon->count() }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pendapatan Bulan Ini</p>
                <div class="p-2 bg-green-50 text-green-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-3">Rp{{ number_format($pendapatanBulanIni, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Booking Dibuat Bulan Ini</p>
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-3">{{ $totalBookingBulanIni }}</p>
        </div>
    </div>

    <!-- Response Needed Section -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-8">
        <h2 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <span>Perlu Direspon Segera</span>
            <span class="text-xs font-medium bg-orange-100 text-orange-700 px-2.5 py-0.5 rounded-full">{{ $perluDirespon->count() }}</span>
        </h2>
        <div class="divide-y divide-gray-100">
            @forelse($perluDirespon as $booking)
                @php
                    $adaChat = $booking->chat_belum_dibaca > 0;
                    $adaKonfirmasi = $booking->status === 'menunggu_konfirmasi';
                    $sesiTerdekat = $booking->sessions->first();
                @endphp
                <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 first:pt-0 last:pb-0 hover:bg-gray-50/50 px-2 rounded-lg transition-colors">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-sm font-semibold text-gray-900">{{ $booking->user->nama }}</p>
                            <span class="text-xs text-gray-400">·</span>
                            <p class="text-xs text-gray-500">{{ $booking->package->category->nama_kategori ?? '-' }} — {{ $booking->package->nama_paket }}</p>
                        </div>

                        <div class="flex items-center gap-1.5 flex-wrap mt-1.5">
                            @if($adaKonfirmasi)
                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold bg-orange-50 text-orange-700 border border-orange-200 px-2 py-0.5 rounded-full">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Menunggu Konfirmasi Harga
                                </span>
                            @endif
                            @if($adaChat)
                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded-full">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    {{ $booking->chat_belum_dibaca }} pesan baru
                                </span>
                            @endif
                            @if($sesiTerdekat)
                                <span class="text-[10px] text-gray-400">
                                    Sesi: {{ \Carbon\Carbon::parse($sesiTerdekat->tanggal)->translatedFormat('d M Y') }}
                                </span>
                            @endif
                        </div>

                        @if($adaChat && $booking->pesan_terakhir)
                            <p class="text-xs text-gray-600 mt-1.5 line-clamp-1 italic">
                                "{{ $booking->pesan_terakhir }}"
                                <span class="text-gray-400 not-italic">· {{ $booking->waktu_pesan_terakhir->diffForHumans() }}</span>
                            </p>
                        @endif

                        @if($adaKonfirmasi)
                            <p class="text-xs text-gray-500 mt-1.5">
                                Total sementara: <span class="font-semibold text-gray-800">Rp{{ number_format($booking->total_harga, 0, ',', '.') }}</span>
                            </p>
                        @endif
                    </div>

                    @if($adaChat)
                        <a href="{{ route('admin.chat.show', $booking->id) }}" class="shrink-0 text-xs font-medium bg-blue-600 text-white rounded-lg px-3.5 py-1.5 hover:bg-blue-700 transition-colors text-center">
                            Balas Chat
                        </a>
                    @else
                        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="shrink-0 text-xs font-medium border border-gray-300 rounded-lg px-3.5 py-1.5 hover:bg-gray-100 text-gray-700 transition-colors text-center">
                            Lihat Booking
                        </a>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-400 py-4 text-center italic">Tidak ada booking yang perlu direspon saat ini.</p>
            @endforelse
        </div>
    </div>

    <!-- Category Breakdown Grid -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Booking per Kategori Bulan Ini</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach($bookingPerKategori as $item)
                <div class="bg-gray-50/60 rounded-xl border border-gray-200/80 p-4 transition-colors hover:bg-gray-50">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $item->nama_kategori }}</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ $item->jumlah }} <span class="text-xs font-normal text-gray-400">booking</span></p>
                </div>
            @endforeach
        </div>
    </div>
</div>