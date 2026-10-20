<div class="min-h-screen w-full bg-[#000000] text-[#fcfdff] flex items-center justify-center p-6 relative overflow-hidden font-sans">
    <!-- Atmospheric Glow -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-2xl h-[500px] bg-[radial-gradient(ellipse_at_top,_rgba(255,128,31,0.22)_0%,_transparent_75%)] pointer-events-none"></div>

    <div class="bg-[#0a0a0c] border border-white/14 rounded-xl p-8 md:p-10 relative z-10 shadow-2xl backdrop-blur-sm" style="max-width: 440px; width: 100%;">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-medium tracking-tight text-white mb-2 font-display-lg" style="font-feature-settings: 'ss01', 'ss04', 'ss11';">Rikoza Studio</h1>
            <p class="text-xs text-white/50 tracking-widest uppercase font-mono">Panel Admin</p>
        </div>

        @if ($error)
            <div class="bg-red-950/60 border border-red-500/30 text-red-400 text-xs px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ $error }}</span>
            </div>
        @endif

        <form wire:submit="login" class="space-y-6">
            <div>
                <label class="block text-xs font-medium text-white/70 mb-2 tracking-wide">Email</label>
                <input type="email" wire:model="email" class="w-full bg-[#06060a] border border-white/14 rounded-lg px-4 py-3 text-sm text-white placeholder-white/30 focus:outline-none focus:border-white transition-colors" placeholder="admin@studio.com" required style="width: 100%;">
                @error('email') <span class="text-red-400 text-xs mt-1.5 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-white/70 mb-2 tracking-wide">Password</label>
                <input type="password" wire:model="password" class="w-full bg-[#06060a] border border-white/14 rounded-lg px-4 py-3 text-sm text-white placeholder-white/30 focus:outline-none focus:border-white transition-colors" placeholder="••••••••" required style="width: 100%;">
                @error('password') <span class="text-red-400 text-xs mt-1.5 block">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full bg-[#fcfdff] text-[#000000] font-medium py-3 rounded-lg text-sm hover:bg-[#f1f7fe] active:scale-[0.99] transition-all cursor-pointer shadow-sm mt-2" style="width: 100%;">
                Masuk ke Dashboard
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-white/6 text-center">
            <a href="/" class="text-xs text-white/40 hover:text-white/80 transition-colors inline-flex items-center gap-1.5">
                &larr; Kembali ke Halaman Utama
            </a>
        </div>
    </div>
</div>