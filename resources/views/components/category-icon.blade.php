@props(['slug'])

<svg {{ $attributes->merge(['class' => 'w-5 h-5']) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    @switch($slug)
        @case('kuliner')
            <path d="M8.25 3v5.25a2.25 2.25 0 004.5 0V3M6 3v18m4.5-12v12M15 3v8.25a3 3 0 003 3V21M18 3v8.25" />
            @break

        @case('budaya-sejarah')
            <path d="M3 21h18M4.5 21V9.75l7.5-4.5 7.5 4.5V21M9 21v-6h6v6M9 12h.008M15 12h.008" />
            @break

        @case('ruang-publik-malam')
            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
            @break

        @case('penginapan')
            <path d="M3 21V8.25L12 3l9 5.25V21M3 21h18M8.25 21v-6a1.5 1.5 0 013 0v6" />
            @break

        @case('belanja-oleh-oleh')
            <path d="M6 7.5V6a3 3 0 016 0v1.5M4.5 7.5h9l.75 12h-10.5l.75-12z" />
            @break

        @case('kampung-wisata-kerajinan')
            <path d="M2.25 21h19.5M4.5 21V9.75L9 6l4.5 3.75V21M13.5 21v-7.5L18 9l3.75 3v9M9 21v-4.5h1.5V21" />
            @break

        @case('wisata-alam')
            <path d="M3 20.25l6-12 3.75 6M9 20.25h12l-5.25-9L13.5 15M15.75 20.25l3-6 2.25 6" />
            @break

        @default
            <path d="M12 3l9 9-9 9-9-9 9-9z" />
    @endswitch
</svg>
