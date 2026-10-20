@props(['href' => null, 'type' => 'button'])

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'bg-surface-elevated text-ink font-button-md rounded-md px-[16px] h-[36px] inline-flex items-center justify-center border border-hairline-strong hover:bg-surface-light hover:text-primary-on transition-colors cursor-pointer text-decoration-none']) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => 'bg-surface-elevated text-ink font-button-md rounded-md px-[16px] h-[36px] inline-flex items-center justify-center border border-hairline-strong hover:bg-surface-light hover:text-primary-on transition-colors cursor-pointer']) }}>
        {{ $slot }}
    </button>
@endif
