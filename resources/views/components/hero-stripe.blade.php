<section class="bg-canvas text-ink w-full px-xxl py-band flex flex-col items-center text-center relative overflow-hidden">
    <!-- Atmospheric glow at the top of the section -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-3xl h-[600px] bg-[radial-gradient(ellipse_at_top,_var(--color-accent-orange-glow)_0%,_transparent_70%)] pointer-events-none"></div>
    
    <div class="relative z-10 max-w-4xl flex flex-col items-center">
        @if(isset($subtitle))
            <h2 class="font-subtitle text-subtitle text-charcoal mb-4" style="font-feature-settings: 'ss01', 'ss04', 'ss11';">{{ $subtitle }}</h2>
        @endif
        
        <h1 class="font-display-xxl text-display-xxl leading-[1.0] tracking-display-xxl mb-8" style="font-feature-settings: 'ss01', 'ss04', 'ss11';">
            {{ $title }}
        </h1>
        
        <div class="font-body-lg text-body-lg text-body max-w-2xl mb-8">
            {{ $slot }}
        </div>
        
        @if(isset($actions))
            <div class="flex items-center justify-center gap-4">
                {{ $actions }}
            </div>
        @endif
    </div>
</section>
