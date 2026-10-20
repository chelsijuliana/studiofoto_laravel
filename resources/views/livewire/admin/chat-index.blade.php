<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Chat Pelanggan</h1>
            <p class="text-sm text-gray-400 mt-0.5">{{ $threads->count() }} percakapan aktif</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="flex gap-2 mb-5">
        <button wire:click="setFilter('semua')"
            class="text-xs px-4 py-2 rounded-xl font-medium transition-all
            {{ $filter === 'semua' ? 'bg-gray-900 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            Semua
        </button>
        <button wire:click="setFilter('belum_dibalas')"
            class="text-xs px-4 py-2 rounded-xl font-medium transition-all flex items-center gap-1.5
            {{ $filter === 'belum_dibalas' ? 'bg-red-500 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            Belum Dibalas
            @php $totalBelumDibalas = $threads->sum('belum_dibalas'); @endphp
            @if($totalBelumDibalas > 0)
                <span class="text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center
                    {{ $filter === 'belum_dibalas' ? 'bg-white text-red-500' : 'bg-red-500 text-white' }}">
                    {{ $totalBelumDibalas > 9 ? '9+' : $totalBelumDibalas }}
                </span>
            @endif
        </button>
    </div>

    {{-- List --}}
    <div class="space-y-2">
        @forelse($threads as $item)
            @php
                $booking      = $item->booking;
                $user         = $booking->user;
                $pesan        = $item->pesan_terakhir;
                $belumDibalas = $item->belum_dibalas;
                $isCustomer   = $pesan->sender_type === 'customer';
                $initials     = strtoupper(substr($user->nama, 0, 2));
                $statusColor  = match($booking->status) {
                    'confirmed','hasil_siap'                       => 'bg-green-100 text-green-700',
                    'menunggu_konfirmasi','menunggu_pembayaran_dp' => 'bg-orange-100 text-orange-700',
                    'selesai'                                      => 'bg-gray-100 text-gray-500',
                    'dibatalkan'                                   => 'bg-red-100 text-red-600',
                    default                                        => 'bg-blue-100 text-blue-700',
                };
                $accentColor  = match($booking->status) {
                    'confirmed','hasil_siap'                       => 'bg-green-400',
                    'menunggu_konfirmasi','menunggu_pembayaran_dp' => 'bg-orange-400',
                    'selesai'                                      => 'bg-gray-300',
                    'dibatalkan'                                   => 'bg-red-400',
                    default                                        => 'bg-blue-400',
                };
            @endphp
            <a href="{{ route('admin.chat.show', $booking->id) }}"
               class="flex items-center gap-4 bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4 hover:shadow-md hover:border-gray-200 transition-all group overflow-hidden relative">

                {{-- Garis aksen kiri --}}
                <div class="absolute left-0 top-0 bottom-0 w-1 {{ $accentColor }} rounded-l-2xl"></div>

                {{-- Konten --}}
                <div class="flex-1 min-w-0 ml-2">
                    {{-- Baris 1: Nama + Status + Waktu --}}
                    <div class="flex items-center justify-between gap-3 mb-1">
                        <div class="flex items-center gap-2 min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate">{{ $user->nama }}</p>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-lg whitespace-nowrap {{ $statusColor }}">
                                {{ $this->statusLabels[$booking->status] ?? $booking->status }}
                            </span>
                            @if($belumDibalas > 0)
                                <span class="w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center flex-shrink-0">
                                    {{ $belumDibalas > 9 ? '9+' : $belumDibalas }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 flex-shrink-0">{{ $pesan->created_at->diffForHumans() }}</p>
                    </div>

                    {{-- Baris 2: Kategori · Paket --}}
                    <div class="flex items-center gap-1.5 mb-1.5">
                        <span class="text-xs font-medium text-indigo-600">{{ $booking->package->category->nama_kategori ?? '' }}</span>
                        <span class="text-gray-200 text-xs">·</span>
                        <span class="text-xs text-gray-500">{{ $booking->package->nama_paket }}</span>
                    </div>

                    {{-- Baris 3: Preview pesan --}}
                    <div class="flex items-center gap-1">
                        @if(!$isCustomer)
                            <span class="text-xs text-gray-400 flex-shrink-0 font-medium">Kamu:</span>
                        @endif
                        <p class="text-xs truncate {{ $belumDibalas > 0 ? 'font-semibold text-gray-800' : 'text-gray-400' }}">
                            {{ $pesan->pesan }}
                        </p>
                    </div>
                </div>

                {{-- Panah --}}
                <svg class="w-4 h-4 text-gray-300 flex-shrink-0 group-hover:text-gray-500 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm py-20 text-center">
                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-600">
                    {{ $filter === 'belum_dibalas' ? 'Semua chat sudah dibalas.' : 'Belum ada chat.' }}
                </p>
            </div>
        @endforelse
    </div>
</div>