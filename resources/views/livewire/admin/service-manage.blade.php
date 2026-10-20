<div>
    <!-- Page Title & Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Layanan & Paket Studio</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola kategori foto, daftar paket harga, fasilitas, dan layanan add-on.</p>
        </div>
        <button wire:click="bukaFormTambahKategori" class="bg-gray-900 text-white text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-gray-800 transition-all shadow-sm flex items-center gap-2 cursor-pointer shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Kategori Baru</span>
        </button>
    </div>

    <!-- Flash Messages -->
    @if (session('message'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-6 flex items-center gap-2 shadow-2xs">
            <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-6 flex items-center gap-2 shadow-2xs">
            <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Category CRUD Cards Stack -->
    <div class="space-y-8">
        @foreach($categories as $category)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 md:p-8 space-y-6 transition-all hover:shadow-md">
                
                <!-- Category Top Card Header -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-6 border-b border-gray-200/80">
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-xl font-bold text-gray-900 tracking-tight">{{ $category->nama_kategori }}</h2>
                            <span class="bg-gray-100 text-gray-700 text-xs font-semibold px-2.5 py-1 rounded-full border border-gray-200">
                                {{ $category->packages->count() }} Paket
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 mt-2 text-xs text-gray-500">
                            <span class="inline-flex items-center gap-1 bg-gray-50 px-2.5 py-1 rounded-md border border-gray-200/60">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Kuota Harian: <strong class="text-gray-800">{{ $category->kuota_harian ?? 'Unlimited' }}</strong>
                            </span>
                            <span class="inline-flex items-center gap-1 bg-gray-50 px-2.5 py-1 rounded-md border border-gray-200/60">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                DP Required: <strong class="text-gray-800">{{ strtoupper($category->dp_type) }} {{ $category->dp_type !== 'none' ? '('.number_format($category->dp_value).')' : '' }}</strong>
                            </span>
                            @if($category->butuh_ruangan)
                                <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 px-2.5 py-1 rounded-md border border-blue-200 font-medium">
                                    Studio Room Needed
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Category Action Buttons -->
                    <div class="flex flex-wrap items-center gap-2.5">
                        <button wire:click="bukaFormEditKategori({{ $category->id }})" class="text-xs font-medium border border-gray-300 bg-white rounded-lg px-3.5 py-2 text-gray-700 hover:bg-gray-50 transition-all cursor-pointer shadow-2xs flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            <span>Edit Pengaturan</span>
                        </button>
                        <button wire:click="bukaFormTambah({{ $category->id }})" class="text-xs font-medium bg-gray-900 text-white rounded-lg px-3.5 py-2 hover:bg-gray-800 transition-all cursor-pointer shadow-2xs flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span> Tambah Paket</span>
                        </button>
                        <button wire:click="hapusKategori({{ $category->id }})" wire:confirm="Yakin hapus kategori ini beserta semua paketnya?" class="text-xs font-medium border border-red-200 bg-red-50 text-red-600 rounded-lg px-3 py-2 hover:bg-red-100 transition-all cursor-pointer shadow-2xs flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            <span>Hapus</span>
                        </button>
                    </div>
                </div>

                <!-- Desktop Package List Table -->
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Daftar Paket Harga</h3>
                    <div class="border border-gray-200 rounded-xl overflow-hidden shadow-2xs bg-white">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="bg-gray-50/80 border-b border-gray-200 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    <th class="py-3.5 px-5">Nama Paket</th>
                                    <th class="py-3.5 px-5">Harga (IDR)</th>
                                    <th class="py-3.5 px-5">Fasilitas Included</th>
                                    <th class="py-3.5 px-5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($category->packages as $package)
                                    <tr class="hover:bg-gray-50/70 transition-colors">
                                        <td class="py-4 px-5">
                                            <p class="font-bold text-gray-900 text-base">{{ $package->nama_paket }}</p>
                                            @if($package->durasi_menit || $package->max_orang)
                                                <p class="text-xs text-gray-500 mt-0.5">
                                                    {{ $package->durasi_menit ? $package->durasi_menit.' menit' : '' }}
                                                    {{ $package->max_orang ? '• max '.$package->max_orang.' orang' : '' }}
                                                </p>
                                            @endif
                                        </td>
                                        <td class="py-4 px-5 font-bold text-gray-900 text-base">
                                            Rp{{ number_format($package->harga, 0, ',', '.') }}
                                        </td>
                                        <td class="py-4 px-5">
                                            <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-700 text-xs font-medium px-2.5 py-1 rounded-md border border-gray-200">
                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                {{ $package->facilities->count() }} Facilities Included
                                            </span>
                                        </td>
                                        <td class="py-4 px-5 text-right space-x-2">
                                            <button wire:click="bukaFormEdit({{ $package->id }})" class="text-xs font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 border border-blue-200 px-3 py-1.5 rounded-lg transition-all cursor-pointer">Edit Paket</button>
                                            <button wire:click="hapusPaket({{ $package->id }})" wire:confirm="Yakin hapus paket {{ $package->nama_paket }}?" class="text-xs font-semibold text-red-600 hover:text-red-800 bg-red-50 border border-red-200 px-3 py-1.5 rounded-lg transition-all cursor-pointer">Hapus</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-6 text-center text-gray-400 text-xs italic">Belum ada paket yang ditambahkan pada kategori ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add-on Items Section Grid -->
                <div class="pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Add-On & Layanan Tambahan</h3>
                        <button wire:click="bukaFormTambahAddon({{ $category->id }})" class="text-xs font-medium text-blue-600 hover:text-blue-800 flex items-center gap-1 transition-colors cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>Tambah Add-On</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @forelse($category->addons as $addon)
                            <div class="flex items-center justify-between text-xs p-3.5 rounded-xl border border-gray-200/80 bg-gray-50/50 hover:bg-gray-50 transition-all shadow-2xs">
                                <div>
                                    <p class="font-bold text-gray-900 text-sm">{{ $addon->nama_addon }}</p>
                                    <span class="inline-block bg-gray-200/70 text-gray-600 text-[10px] uppercase font-semibold px-2 py-0.5 rounded mt-1">
                                        {{ $addon->tipe }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900 text-sm">Rp{{ number_format($addon->harga, 0, ',', '.') }}</p>
                                    <div class="flex items-center gap-2 mt-1 justify-end">
                                        <button wire:click="bukaFormEditAddon({{ $addon->id }})" class="text-blue-600 hover:text-blue-800 font-medium cursor-pointer">Edit</button>
                                        <span class="text-gray-300">•</span>
                                        <button wire:click="hapusAddon({{ $addon->id }})" wire:confirm="Yakin hapus add-on ini?" class="text-red-600 hover:text-red-800 font-medium cursor-pointer">Hapus</button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 italic col-span-3">Belum ada add-on untuk kategori ini.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        @endforeach
    </div>

    {{-- Desktop Modals with Explicit Width Sizing --}}

    {{-- Modal Paket --}}
    @if($showPackageForm)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center z-50 p-4 overflow-y-auto" wire:click.self="batal">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 my-auto overflow-hidden relative z-10 flex flex-col" style="max-width: 560px; width: 100%; max-height: 88vh;">
                <div class="p-5 border-b border-gray-200/80 flex justify-between items-center bg-gray-50/80 shrink-0">
                    <h3 class="font-bold text-gray-900 text-base">{{ $editingPackageId ? 'Edit Paket Foto' : 'Tambah Paket Foto Baru' }}</h3>
                    <button wire:click="batal" class="text-gray-400 hover:text-gray-600 text-xl font-bold p-1 cursor-pointer">&times;</button>
                </div>
                <div class="space-y-4 p-6 overflow-y-auto flex-1 text-left">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Nama Paket</label>
                        <input type="text" wire:model="namaPaket" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900" style="width: 100%;">
                        @error('namaPaket') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Harga (IDR)</label>
                        <input type="number" wire:model="harga" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900" style="width: 100%;">
                        @error('harga') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    @php
                        $namaKatSkrg = strtolower(trim($this->formKategoriNama));
                        // Durasi disembunyikan buat: Wedding, Pass Foto, Foto Latar Biru
                        $tampilDurasi = ! in_array($namaKatSkrg, ['wedding pack', 'pass foto', 'foto latar biru']);
                        // Maksimal Orang disembunyikan buat: Wedding, Maternity, Pre Wedding, Pass Foto, Foto Latar Biru
                        $tampilMaksOrang = ! in_array($namaKatSkrg, ['wedding pack', 'maternity pack', 'pre wedding package', 'pass foto', 'foto latar biru']);
                    @endphp
                    @if($tampilDurasi || $tampilMaksOrang)
                        <div class="grid {{ ($tampilDurasi && $tampilMaksOrang) ? 'grid-cols-2' : 'grid-cols-1' }} gap-4">
                            @if($tampilDurasi)
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Durasi (Menit)</label>
                                    <input type="number" wire:model="durasiMenit" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900" style="width: 100%;">
                                </div>
                            @endif
                            @if($tampilMaksOrang)
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Maksimal Orang</label>
                                    <input type="number" wire:model="maxOrang" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900" style="width: 100%;">
                                </div>
                            @endif
                        </div>
                    @endif
                    @php
                        // Cakupan Acara cuma relevan buat Wedding, jadi disembunyikan
                        // buat semua kategori lain.
                        $tampilCakupan = ! in_array($namaKatSkrg, [
                            'family package', 'graduation package', 'maternity pack',
                            'pre wedding package', 'pass foto', 'foto latar biru',
                        ]);
                    @endphp
                    @if($tampilCakupan)
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Cakupan Acara</label>
                            <input type="text" wire:model="cakupanAcara" placeholder="pesta_saja / akad_pesta" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900" style="width: 100%;">
                        </div>
                    @endif
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Fasilitas (1 per baris, format: nama:jumlah)</label>
                        <textarea wire:model="fasilitasText" rows="4" placeholder="Album Exclusive:1&#10;Cetak foto 4R:100" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900 font-mono text-xs" style="width: 100%;"></textarea>
                    </div>

                    @php
                        $namaKategoriNormalisasi = strtolower(trim($this->formKategoriNama));
                        $kategoriSesi = in_array($namaKategoriNormalisasi, ['family package', 'graduation package']);
                        $kategoriMaternity = $namaKategoriNormalisasi === 'maternity pack';
                        $kategoriLatarBiru = str_contains($namaKategoriNormalisasi, 'latar biru');
                    @endphp

                    @if($kategoriSesi || $kategoriMaternity || $kategoriLatarBiru)
                        <div class="pt-4 border-t border-gray-200 space-y-3">
                            <p class="text-xs font-bold text-gray-700 uppercase tracking-wide">Pengaturan Tambahan Kategori</p>

                            @if($kategoriSesi)
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Jumlah sesi</label>
                                        <input type="number" wire:model="jumlahSesi" class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Pilihan latar</label>
                                        <input type="number" wire:model="pilihanLatar" class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Jepretan per sesi</label>
                                        <input type="text" wire:model="jepretanPerSesi" class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Durasi per sesi</label>
                                        <input type="text" wire:model="durasiPerSesi" class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Jenis sesi (1 per baris)</label>
                                    <textarea wire:model="jenisSesiText" rows="2" placeholder="Sesi Foto Keluarga&#10;Sesi Foto Ayah Ibu"
                                        class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
                                </div>
                            @endif

                            @if($kategoriMaternity)
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Pilihan latar</label>
                                        <input type="number" wire:model="pilihanLatar" class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Maksimal kostum</label>
                                        <input type="number" wire:model="maksKostum" class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    </div>
                                </div>
                            @endif

                            @if($kategoriLatarBiru)
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Sesi tambahan (1 per baris)</label>
                                    <textarea wire:model="jenisSesiText" rows="2" placeholder="Sesi Pose Bebas 25x Jepret"
                                        class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="flex gap-3 p-5 border-t border-gray-200/80 bg-gray-50/80 shrink-0">
                    <button wire:click="batal" class="flex-1 border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer">Batal</button>
                    <button wire:click="simpanPaket" class="flex-1 bg-gray-900 text-white rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-gray-800 transition-colors cursor-pointer">Simpan Paket</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Kategori --}}
    @if($showCategoryForm)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center z-50 p-4 overflow-y-auto" wire:click.self="batal">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 p-6 md:p-8 my-auto relative z-10 text-left" style="max-width: 480px; width: 100%;">
                <h3 class="font-bold text-gray-900 text-lg mb-5 pb-3 border-b border-gray-100">{{ $editingCategoryId ? 'Edit Kategori' : 'Tambah Kategori Baru' }}</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Nama Kategori</label>
                        <input type="text" wire:model="namaKategori" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900" style="width: 100%;">
                        @error('namaKategori') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Kuota Harian (Kosongkan jika Unlimited)</label>
                        <input type="number" wire:model="kuotaHarian" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900" style="width: 100%;">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Tipe Down Payment (DP)</label>
                        <select wire:model="dpType" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900" style="width: 100%;">
                            <option value="none">Tidak ada DP</option>
                            <option value="flat">Nominal Tetap (Rp)</option>
                            <option value="percentage">Persentase (%)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Nilai DP</label>
                        <input type="number" wire:model="dpValue" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900" style="width: 100%;">
                        @error('dpValue') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <label class="flex items-start gap-3 text-xs text-gray-700 cursor-pointer pt-2">
                        <input type="checkbox" wire:model="butuhRuangan" class="rounded border-gray-300 text-gray-900 mt-0.5 w-4 h-4">
                        <span>Membutuhkan ruangan studio fisik (uncheck untuk sesi luar/Wedding)</span>
                    </label>
                </div>
                <div class="flex gap-3 mt-6 pt-5 border-t border-gray-200/80">
                    <button wire:click="batal" class="flex-1 border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">Batal</button>
                    <button wire:click="simpanKategori" class="flex-1 bg-gray-900 text-white rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-gray-800 transition-colors cursor-pointer">Simpan Kategori</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Add-on --}}
    @if($showAddonForm)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center z-50 p-4 overflow-y-auto" wire:click.self="batal">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 p-6 md:p-8 my-auto relative z-10 text-left" style="max-width: 480px; width: 100%;">
                <h3 class="font-bold text-gray-900 text-lg mb-5 pb-3 border-b border-gray-100">{{ $editingAddonId ? 'Edit Add-on' : 'Tambah Add-on Baru' }}</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Nama Add-on</label>
                        <input type="text" wire:model="namaAddon" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900" style="width: 100%;">
                        @error('namaAddon') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Harga (IDR)</label>
                        <input type="number" wire:model="hargaAddon" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900" style="width: 100%;">
                        @error('hargaAddon') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Tipe Perhitungan</label>
                        <select wire:model="tipeAddon" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900" style="width: 100%;">
                            <option value="flat">Flat (Harga Tetap)</option>
                            <option value="per_sesi">Per Sesi Tambahan</option>
                            <option value="per_orang">Per Orang Tambahan</option>
                            <option value="nego">Nego (Harga Ditentukan Admin Nanti)</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-3 mt-6 pt-5 border-t border-gray-200/80">
                    <button wire:click="batal" class="flex-1 border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">Batal</button>
                    <button wire:click="simpanAddon" class="flex-1 bg-gray-900 text-white rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-gray-800 transition-colors cursor-pointer">Simpan Add-on</button>
                </div>
            </div>
        </div>
    @endif
</div>