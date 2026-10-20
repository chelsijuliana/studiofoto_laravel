<nav class="w-full h-[64px] bg-canvas text-body font-button-sm border-b border-hairline flex items-center justify-between px-8">
    <div class="flex items-center gap-8">
        <a href="/" class="font-display-lg text-ink text-xl tracking-tight">Rikoza Studio</a>
        <div class="hidden md:flex items-center gap-6 text-charcoal">
            <a href="#features" class="hover:text-ink transition-colors">Features</a>
            <a href="#pricing" class="hover:text-ink transition-colors">Pricing</a>
            <a href="#portfolio" class="hover:text-ink transition-colors">Portfolio</a>
        </div>
    </div>
    <div class="flex items-center gap-4">
        @auth
            <a href="{{ route('admin.dashboard') }}" class="text-charcoal hover:text-ink transition-colors">Dashboard</a>
        @else
            <a href="{{ route('admin.login') }}" class="text-charcoal hover:text-ink transition-colors">Sign in</a>
        @endauth
        <x-button-primary href="{{ route('admin.login') }}">Get started</x-button-primary>
    </div>
</nav>
