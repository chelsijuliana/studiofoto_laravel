<div>
    <h1 class="text-xl font-semibold mb-6">Jam operasional</h1>

    @if (session('message'))
        <div class="bg-green-50 text-green-700 text-sm px-3 py-2 rounded mb-4">{{ session('message') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg mb-4 flex items-start gap-2">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @php
        $namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", 'Sabtu'];
    @endphp

    <div class="bg-white rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left text-gray-500">
                    <th class="p-3">Hari</th>
                    <th class="p-3">Jam buka</th>
                    <th class="p-3">Jam tutup</th>
                    <th class="p-3">Tutup</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hours as $index => $h)
                    <tr class="border-b last:border-0">
                        <td class="p-3 font-medium">{{ $namaHari[$h['hari']] }}</td>
                        <td class="p-3">
                            <input type="time" wire:model="hours.{{ $index }}.jam_buka" @disabled($h['is_tutup']) class="border rounded px-2 py-1 text-sm">
                        </td>
                        <td class="p-3">
                            <input type="time" wire:model="hours.{{ $index }}.jam_tutup" @disabled($h['is_tutup']) class="border rounded px-2 py-1 text-sm">
                        </td>
                        <td class="p-3">
                            <input type="checkbox" wire:model="hours.{{ $index }}.is_tutup">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <button wire:click="simpan" class="mt-4 bg-gray-900 text-white text-sm px-5 py-2.5 rounded">Simpan perubahan</button>
</div>