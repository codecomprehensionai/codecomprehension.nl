{{-- resources/views/components/button.blade.php --}}
@props([
    'variant' => 'default',
    'size' => 'default',
    'disabled' => false,
    'loading' => false,
    'loadingText' => 'Loading...'
])

<button
    {{ $attributes->merge([
        'type' => 'button',
        'class' => 'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 ' .
        ($variant === 'default' ? 'bg-primary text-primary-foreground hover:bg-primary/90' : '') .
        ($size === 'default' ? 'h-9 px-4 py-2' : '')
    ]) }}
    @if($disabled || $loading) disabled @endif
>
    @if($loading)
        <svg class="animate-spin -ml-1 mr-3 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        {{ $loadingText }}
    @else
        {{ $slot }}
    @endif
</button>
