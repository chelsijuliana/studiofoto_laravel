<div>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Laporan Transaksi</h1>
            <p class="text-sm text-gray-500 mt-1">Ringkasan pendapatan berdasarkan pembayaran yang sudah berhasil.</p>
        </div>
        <button wire:click="export"
            class="inline-flex items-center gap-2 bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export CSV
        </button>
    </div>

    {{-- Filter periode --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6 flex flex-wrap items-center gap-3">
        <div class="flex bg-gray-100 rounded-lg p-1">
            <button wire:click="$set('mode', 'bulan')"
                class="text-xs px-3 py-1.5 rounded-md font-medium transition-colors {{ $mode === 'bulan' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500' }}">
                Per Bulan
            </button>
            <button wire:click="$set('mode', 'tahun')"
                class="text-xs px-3 py-1.5 rounded-md font-medium transition-colors {{ $mode === 'tahun' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500' }}">
                Per Tahun
            </button>
        </div>

        @if($mode === 'bulan')
            <select wire:model.live="bulan" class="bg-white border border-gray-300 rounded-lg px-3 py-1.5 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
                @foreach(range(1,12) as $b)
                    <option value="{{ $b }}">{{ \Carbon\Carbon::create(2000, $b, 1)->translatedFormat('F') }}</option>
                @endforeach
            </select>
        @endif

        <select wire:model.live="tahun" class="bg-white border border-gray-300 rounded-lg px-3 py-1.5 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
            @foreach(range(now()->year + 1, now()->year - 3) as $y)
                <option value="{{ $y }}">{{ $y }}</option>
            @endforeach
        </select>
    </div>

    {{-- Metrik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center gap-2 text-gray-400 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-xs font-medium uppercase tracking-wide">Booking Lunas</p>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $metrics['total_booking'] }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center gap-2 text-gray-400 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                <p class="text-xs font-medium uppercase tracking-wide">Total Transaksi</p>
            </div>
            <p class="text-2xl font-bold text-gray-900">Rp{{ number_format($metrics['total_transaksi'], 0, ',', '.') }}</p>
        </div>

        <div class="bg-orange-50 rounded-xl border border-orange-200 shadow-sm p-5">
            <div class="flex items-center gap-2 text-orange-500 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                <p class="text-xs font-medium uppercase tracking-wide">Booking Belum Lunas</p>
            </div>
            <p class="text-2xl font-bold text-orange-700">{{ $metrics['booking_belum_lunas'] }}</p>
        </div>

        <div class="bg-orange-50 rounded-xl border border-orange-200 shadow-sm p-5">
            <div class="flex items-center gap-2 text-orange-500 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                <p class="text-xs font-medium uppercase tracking-wide">Total Piutang</p>
            </div>
            <p class="text-2xl font-bold text-orange-700">Rp{{ number_format($metrics['total_piutang'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Tren chart --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-sm font-semibold text-gray-800">
                    Tren Pendapatan {{ $mode === 'bulan' ? 'Harian' : 'Bulanan' }}
                </h2>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $mode === 'bulan' ? \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') : $tahun }}
                    · dari booking yang sudah dibayar
                </p>
            </div>
            @if($tren->count())
                <div class="text-right">
                    <p class="text-xs text-gray-400">Total periode ini</p>
                    <p class="text-sm font-bold text-gray-900">Rp{{ number_format($trenValues->sum(), 0, ',', '.') }}</p>
                </div>
            @endif
        </div>

        <div x-data="{
                chart: null,
                init() {
                    this.$nextTick(() => this.render());
                },
                render() {
                    const canvas = this.$refs.canvas;
                    if (!canvas) return;
                    if (this.chart) {
                        this.chart.destroy();
                        this.chart = null;
                    }
                    const labels = {{ Js::from($trenLabels) }};
                    const values = {{ Js::from($trenValues) }};
                    if (!labels.length) return;
                    const maxVal = Math.max(...values);

                    const formatRupiah = (v) => {
                        if (v >= 1000000) return 'Rp' + (v/1000000).toFixed(1).replace('.0','') + 'jt';
                        if (v >= 1000)    return 'Rp' + (v/1000).toFixed(0) + 'rb';
                        return 'Rp' + v;
                    };

                    const ctx = canvas.getContext('2d');
                    const gradient = ctx.createLinearGradient(0, 0, 0, 260);
                    gradient.addColorStop(0, 'rgba(201,168,76,0.28)');
                    gradient.addColorStop(1, 'rgba(201,168,76,0.02)');

                    this.chart = new Chart(canvas, {
                        type: 'line',
                        data: {
                            labels,
                            datasets: [{
                                label: 'Pendapatan',
                                data: values,
                                borderColor: '#C9A84C',
                                backgroundColor: gradient,
                                fill: true,
                                tension: 0.35,
                                pointRadius: 3,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#C9A84C',
                                pointBorderWidth: 2,
                                borderWidth: 2.5,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            resizeDelay: 200,
                            animation: false,
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#0F0F0F',
                                    padding: 10,
                                    titleFont: { size: 12, weight: '600' },
                                    bodyFont: { size: 12 },
                                    displayColors: false,
                                    callbacks: {
                                        title(items) {
                                            return {{ $mode === 'bulan' ? "'Tanggal ' + items[0].label" : "items[0].label" }};
                                        },
                                        label(item) {
                                            const v = item.parsed.y;
                                            return 'Pendapatan: Rp' + v.toLocaleString('id-ID');
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    suggestedMax: maxVal > 0 ? maxVal * 1.25 : 1000000,
                                    grid: { color: '#F3F4F6' },
                                    border: { display: false },
                                    ticks: {
                                        color: '#9CA3AF',
                                        font: { size: 11 },
                                        callback: (v) => formatRupiah(v)
                                    }
                                },
                                x: {
                                    grid: { display: false },
                                    border: { display: false },
                                    ticks: {
                                        color: '#9CA3AF',
                                        font: { size: 11 },
                                        maxTicksLimit: 15
                                    }
                                }
                            }
                        }
                    });
                }
             }"
             x-init="init()"
             wire:key="chart-{{ $mode }}-{{ $bulan }}-{{ $tahun }}">
            @if($tren->count())
                <div class="h-72">
                    <canvas x-ref="canvas"></canvas>
                </div>
            @else
                <div class="py-12 flex flex-col items-center justify-center text-center">
                    <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/></svg>
                    <p class="text-sm text-gray-400">Belum ada data transaksi di periode ini.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Per kategori --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-800">Pendapatan per Kategori Layanan</h2>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-left text-gray-400 text-xs uppercase tracking-wide">
                    <th class="px-5 py-3 font-medium">Kategori</th>
                    <th class="px-5 py-3 font-medium text-center">Booking</th>
                    <th class="px-5 py-3 font-medium text-right">Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($perKategori as $item)
                    @php $persen = $metrics['total_transaksi'] > 0 ? ($item->pendapatan / $metrics['total_transaksi']) * 100 : 0; @endphp
                    <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/60 transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="font-medium text-gray-800">{{ $item->nama_kategori }}</p>
                            <div class="w-full bg-gray-100 rounded-full h-1 mt-1.5 max-w-[140px]">
                                <div class="bg-[#C9A84C] h-1 rounded-full" style="width: {{ $persen }}%"></div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-center text-gray-600">{{ $item->jumlah_booking }}</td>
                        <td class="px-5 py-3.5 text-right font-semibold text-gray-900">Rp{{ number_format($item->pendapatan, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="p-8 text-center text-gray-400 text-sm">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>