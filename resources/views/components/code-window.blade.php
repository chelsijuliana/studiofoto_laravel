<div {{ $attributes->merge(['class' => 'bg-surface-deep text-body font-code-md rounded-lg p-xl border border-hairline-strong relative w-full overflow-hidden']) }}>
    <!-- Traffic lights -->
    <div class="flex items-center gap-2 mb-6">
        <div class="w-2 h-2 rounded-full bg-accent-red"></div>
        <div class="w-2 h-2 rounded-full bg-accent-yellow"></div>
        <div class="w-2 h-2 rounded-full bg-accent-green"></div>
    </div>
    
    <div class="overflow-x-auto text-sm leading-[1.6]">
        {{ $slot }}
    </div>
</div>
