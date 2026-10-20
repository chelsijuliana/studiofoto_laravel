<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Rikoza Studio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @livewireStyles
</head>
<body class="bg-gray-100 text-gray-900 font-sans antialiased min-h-screen">

    @if(auth()->check())
    <div class="flex min-h-screen">
        <!-- DESIGN.md Styled Sidebar -->
        <aside class="w-64 bg-[#0a0a0c] text-white flex flex-col border-r border-white/14 shrink-0 h-screen sticky top-0 z-30 selection:bg-white selection:text-black">
            <!-- Sidebar Header / Wordmark -->
            <div class="p-5 border-b border-white/10 flex items-center justify-between shrink-0">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#11ff99] shadow-[0_0_8px_rgba(17,255,153,0.6)]"></span>
                        <a href="{{ route('admin.dashboard') }}" class="text-lg font-medium tracking-tight text-white font-display-lg">Rikoza Studio</a>
                    </div>
                    <p class="text-[10px] tracking-widest text-white/40 uppercase font-mono mt-0.5 ml-4">Panel Admin</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 p-3 space-y-1 overflow-y-auto no-scrollbar min-h-0">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all border {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white font-medium border-white/14 shadow-sm' : 'text-white/60 hover:text-white hover:bg-white/5 border-transparent' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.bookings.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all border {{ request()->routeIs('admin.bookings.*') ? 'bg-white/10 text-white font-medium border-white/14 shadow-sm' : 'text-white/60 hover:text-white hover:bg-white/5 border-transparent' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Manajemen booking</span>
                </a>

                <a href="{{ route('admin.chat.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all border {{ request()->routeIs('admin.chat.*') ? 'bg-white/10 text-white font-medium border-white/14 shadow-sm' : 'text-white/60 hover:text-white hover:bg-white/5 border-transparent' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <span>Chat</span>
                </a>

                <a href="{{ route('admin.services.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all border {{ request()->routeIs('admin.services.*') ? 'bg-white/10 text-white font-medium border-white/14 shadow-sm' : 'text-white/60 hover:text-white hover:bg-white/5 border-transparent' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span>Layanan dan paket</span>
                </a>

                <a href="{{ route('admin.schedule.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all border {{ request()->routeIs('admin.schedule.*') ? 'bg-white/10 text-white font-medium border-white/14 shadow-sm' : 'text-white/60 hover:text-white hover:bg-white/5 border-transparent' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Jadwal</span>
                </a>

                <a href="{{ route('admin.reports.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all border {{ request()->routeIs('admin.reports.*') ? 'bg-white/10 text-white font-medium border-white/14 shadow-sm' : 'text-white/60 hover:text-white hover:bg-white/5 border-transparent' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span>Laporan transaksi</span>
                </a>

                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all border {{ request()->routeIs('admin.users.*') ? 'bg-white/10 text-white font-medium border-white/14 shadow-sm' : 'text-white/60 hover:text-white hover:bg-white/5 border-transparent' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span>Manajemen user</span>
                </a>

                <a href="{{ route('admin.portfolio.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all border {{ request()->routeIs('admin.portfolio.*') ? 'bg-white/10 text-white font-medium border-white/14 shadow-sm' : 'text-white/60 hover:text-white hover:bg-white/5 border-transparent' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Portofolio</span>
                </a>

                <a href="{{ route('admin.operating-hours.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all border {{ request()->routeIs('admin.operating-hours.*') ? 'bg-white/10 text-white font-medium border-white/14 shadow-sm' : 'text-white/60 hover:text-white hover:bg-white/5 border-transparent' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Jam operasional</span>
                </a>
            </nav>

            <!-- User Footer / Account Card -->
            <div class="p-3 border-t border-white/10 shrink-0 bg-[#0a0a0c]">
                <div class="p-3 rounded-xl bg-[#101012] border border-white/10 flex items-center justify-between">
                    <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-2.5 overflow-hidden">
                        <div class="w-8 h-8 rounded-full bg-white/10 text-white text-xs font-medium flex items-center justify-center border border-white/14 shrink-0">
                            {{ strtoupper(substr(auth()->user()->nama ?? 'A', 0, 1)) }}
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-medium text-white truncate">{{ auth()->user()->nama }}</p>
                            <p class="text-[10px] text-white/40 truncate">Admin</p>
                        </div>
                    </a>
                    <div x-data="{ showLogoutModal: false }">
                        <form method="POST" action="{{ route('admin.logout') }}" x-ref="logoutForm">
                            @csrf
                        </form>
                        <button type="button" @click="showLogoutModal = true" class="text-white/40 hover:text-red-400 p-1.5 rounded-lg hover:bg-white/5 transition-colors" title="Logout">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>

                        <!-- Modal Konfirmasi Logout -->
                        <div x-show="showLogoutModal" x-cloak
                            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
                            style="display:none">
                            <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm mx-4" @click.outside="showLogoutModal = false">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-11 h-11 rounded-full bg-red-50 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">Konfirmasi Logout</p>
                                        <p class="text-xs text-gray-500">Kamu akan keluar dari panel admin.</p>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-700 mb-5">Yakin ingin keluar dari akun ini?</p>
                                <div class="flex border-t border-gray-100">
                                    <button type="button" @click="showLogoutModal = false"
                                        class="flex-1 py-3 text-sm text-gray-700 hover:bg-gray-50 font-medium rounded-bl-2xl border-r border-gray-100 transition-colors">
                                        Batal
                                    </button>
                                    <button type="button" @click="$refs.logoutForm.submit()"
                                        class="flex-1 py-3 text-sm font-bold text-red-600 hover:bg-red-50 rounded-br-2xl transition-colors">
                                        Ya, Keluar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 p-6 md:p-8 bg-gray-50/80 {{ request()->routeIs('admin.chat.show') ? 'overflow-hidden' : 'overflow-y-auto' }}">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-end mb-6">
                    <livewire:admin.notification-bell />
                </div>
                {{ $slot }}
            </div>
        </main>
    </div>
    @else
        {{ $slot }}
    @endif

    @livewireScripts
</body>
</html>