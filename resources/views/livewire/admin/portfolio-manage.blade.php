<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Portofolio</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola galeri foto yang tampil di halaman pelanggan.</p>
        </div>
        <span class="text-xs font-medium text-gray-400 bg-gray-100 px-3 py-1.5 rounded-full">
            {{ $images->count() }} foto
        </span>
    </div>

    @if (session('message'))
        <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-5">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('message') }}
        </div>
    @endif

    {{-- Form upload --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-sm font-semibold text-gray-800">Upload Foto Baru</p>
        </div>

        <div class="px-6 pt-5 pb-6 space-y-4" x-data="{ previews: [] }">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-gray-500 font-medium block mb-2">Kategori</label>
                    <select wire:model.live="kategori"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="">-- Tanpa kategori --</option>
                        @foreach ($daftarKategori as $kat)
                            <option value="{{ $kat }}">{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 font-medium block mb-2">Pilih Foto</label>
                    <input type="file" wire:model="photos" multiple
                        x-on:change="
                            previews = [];
                            for (const file of $event.target.files) {
                                previews.push(URL.createObjectURL(file));
                            }
                        "
                        class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border file:border-gray-300 file:text-xs file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                </div>
            </div>

            @error('photos.*')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror

            <div wire:loading wire:target="photos" class="text-xs text-gray-400">Mengunggah ke server...</div>

            {{-- Preview foto yang baru dipilih --}}
            <div class="flex flex-wrap gap-2" x-show="previews.length > 0">
                <template x-for="(src, index) in previews" :key="index">
                    <div class="h-24 w-24 shrink-0 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center overflow-hidden">
                        <img :src="src" class="max-h-full max-w-full object-contain">
                    </div>
                </template>
            </div>

            <button wire:click="simpanFoto"
                class="w-full bg-gray-900 text-white text-sm py-2.5 rounded-xl hover:bg-gray-800 transition-colors font-medium">
                Upload
            </button>
        </div>
    </div>

    {{-- Grid foto --}}
    @if($images->count())
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($images as $img)
                <div class="relative group rounded-2xl overflow-hidden border border-gray-200 shadow-sm bg-gray-100 aspect-square">
                    <img src="{{ $img->image_url }}"
                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">

                    {{-- Overlay gradient + kategori, muncul halus di bawah foto --}}
                    @if($img->kategori)
                        <div class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-black/70 via-black/20 to-transparent flex items-end p-3">
                            <span class="text-white text-xs font-semibold tracking-wide">
                                {{ $img->kategori }}
                            </span>
                        </div>
                    @endif

                    {{-- Tombol hapus, muncul saat hover --}}
                    <button wire:click="hapus({{ $img->id }})" wire:confirm="Yakin hapus foto ini?"
                        class="absolute top-2 right-2 w-7 h-7 rounded-full bg-white/90 text-red-600 text-xs shadow flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        &#10005;
                    </button>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm py-16 flex flex-col items-center justify-center">
            <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-sm text-gray-400">Belum ada foto portofolio.</p>
        </div>
    @endif
</div>