<div class="max-w-md">
    <h1 class="text-xl font-semibold mb-6">Profil admin</h1>

    @if (session('message'))
        <div class="bg-green-50 text-green-700 text-sm px-3 py-2 rounded mb-4">{{ session('message') }}</div>
    @endif

    <div class="bg-white rounded-lg p-4 mb-6">
        <p class="text-sm font-medium mb-3">Data akun</p>
        <div class="space-y-3">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Nama</label>
                <input type="text" wire:model="nama" class="w-full border rounded px-3 py-2 text-sm">
                @error('nama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Email</label>
                <input type="email" wire:model="email" class="w-full border rounded px-3 py-2 text-sm">
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">No. HP</label>
                <input type="text" wire:model="noHp" class="w-full border rounded px-3 py-2 text-sm">
            </div>
        </div>
        <button wire:click="simpanProfil" class="mt-4 w-full bg-gray-900 text-white text-sm py-2 rounded">Simpan profil</button>
    </div>

    <div class="bg-white rounded-lg p-4">
        <p class="text-sm font-medium mb-3">Ganti password</p>
        <div class="space-y-3">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Password lama</label>
                <input type="password" wire:model="passwordLama" class="w-full border rounded px-3 py-2 text-sm">
                @error('passwordLama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Password baru</label>
                <input type="password" wire:model="passwordBaru" class="w-full border rounded px-3 py-2 text-sm">
                @error('passwordBaru') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Konfirmasi password baru</label>
                <input type="password" wire:model="passwordBaru_confirmation" class="w-full border rounded px-3 py-2 text-sm">
            </div>
        </div>
        <button wire:click="gantiPassword" class="mt-4 w-full bg-gray-900 text-white text-sm py-2 rounded">Ganti password</button>
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('password-changed', () => {
            alert('Password berhasil diubah!');
        });
        Livewire.on('profile-saved', () => {
            alert('Profil berhasil disimpan!');
        });
    });
</script>