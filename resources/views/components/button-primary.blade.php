@props(['href' => null, 'type' => 'button'])

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'bg-primary text-primary-on font-button-md rounded-md px-[16px] h-[36px] inline-flex items-center justify-center hover:bg-surface-light transition-colors cursor-pointer text-decoration-none']) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => 'bg-primary text-primary-on font-button-md rounded-md px-[16px] h-[36px] inline-flex items-center justify-center hover:bg-surface-light transition-colors cursor-pointer']) }}>
        {{ $slot }}
    </button>
@endif
