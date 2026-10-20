<div>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen User</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data pelanggan yang terdaftar di aplikasi.</p>
        </div>
        <input type="text" wire:model.live.debounce.400ms="search"
            placeholder="Cari nama atau email..."
            class="bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900 shadow-sm w-full md:w-72">
    </div>

    {{-- Flash messages --}}
    @if (session('message'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('message') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Tabel --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-200 text-xs font-semibold uppercase tracking-wider text-gray-500">
                        <th class="py-3.5 px-4">Nama</th>
                        <th class="py-3.5 px-4">Email</th>
                        <th class="py-3.5 px-4">No. HP</th>
                        <th class="py-3.5 px-4">Role</th>
                        <th class="py-3.5 px-4">Total Booking</th>
                        <th class="py-3.5 px-4">Terdaftar</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-gray-900 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                                        {{ strtoupper(substr($user->nama, 0, 2)) }}
                                    </div>
                                    <span class="font-semibold text-gray-900">{{ $user->nama }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-gray-700">{{ $user->email }}</td>
                            <td class="py-3.5 px-4 text-gray-600">{{ $user->no_hp ?? '-' }}</td>
                            <td class="py-3.5 px-4">
                                <span class="text-xs font-medium border px-2.5 py-1 rounded-full bg-gray-100 text-gray-700 border-gray-200">
                                    Pelanggan
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-medium text-gray-900">{{ $user->bookings_count }}</span>
                                @if($user->bookings_count > 0)
                                    <span class="text-xs text-gray-400 ml-1">booking</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-xs text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="py-3.5 px-4 text-right">
                                <button
                                    wire:click="bukaKonfirmasiHapus({{ $user->id }})"
                                    class="text-xs text-red-600 hover:text-red-700 font-medium border border-red-200 hover:border-red-300 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400 text-sm italic">Belum ada user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>

    {{-- Modal konfirmasi hapus --}}
    @if($konfirmasiHapusId)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-11 h-11 rounded-full bg-red-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Hapus Akun User</p>
                            <p class="text-xs text-gray-500">Tindakan ini tidak bisa dibatalkan.</p>
                        </div>
                    </div>

                    <p class="text-sm text-gray-700 mb-2">
                        Yakin ingin menghapus akun <strong class="text-gray-900">{{ $konfirmasiHapusNama }}</strong>?
                    </p>
                    <p class="text-xs text-red-600 bg-red-50 border border-red-100 rounded-lg px-3 py-2 mb-5">
                        ⚠️ Semua data booking, chat, dan riwayat transaksi akun ini juga akan ikut terhapus permanen.
                    </p>
                </div>

                {{-- Tombol selalu di bawah, tidak terpotong --}}
                <div class="flex border-t border-gray-100">
                    <button
                        wire:click="batalHapus"
                        class="flex-1 py-3.5 text-sm text-gray-700 hover:bg-gray-50 font-medium rounded-bl-xl transition-colors border-r border-gray-100">
                        Batal
                    </button>
                    <button
                        wire:click="hapusUser"
                        class="flex-1 py-3.5 text-sm text-red-600 hover:bg-red-50 font-bold rounded-br-xl transition-colors">
                        Ya, Hapus Akun
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>