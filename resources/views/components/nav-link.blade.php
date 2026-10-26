@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3 py-2 text-sm font-semibold text-ink transition duration-150 ease-in-out'
            : 'inline-flex items-center px-3 py-2 text-sm font-medium text-ink/50 hover:text-ink transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
