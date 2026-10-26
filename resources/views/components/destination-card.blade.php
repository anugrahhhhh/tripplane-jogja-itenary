@props(['destination'])

@php
    $rating = $destination->reviews_avg_rating ?? $destination->averageRating();
    $reviewsCount = $destination->reviews_count ?? $destination->reviewsCount();
    $area = \Illuminate\Support\Str::before($destination->address, ',');
    $isFavorited = $destination->isFavoritedBy(auth()->user());
@endphp

<div class="group relative bg-white rounded-2xl border border-primary-100 overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition duration-200">
    <a href="{{ route('destinations.show', $destination) }}" class="block">
        <div class="relative aspect-[4/3] bg-primary-50 overflow-hidden">
            @if ($destination->cover_photo)
                <img src="{{ asset('storage/'.$destination->cover_photo) }}" alt="{{ $destination->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy">
            @else
                <div class="w-full h-full flex items-center justify-center text-primary-300">
                    <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 21h18a1.5 1.5 0 001.5-1.5V4.5A1.5 1.5 0 0021 3H3a1.5 1.5 0 00-1.5 1.5v15A1.5 1.5 0 003 21z" />
                    </svg>
                </div>
            @endif

            @isset($destination->distance_km)
                <span class="absolute top-3 left-3 inline-flex items-center rounded-full bg-primary-900/80 backdrop-blur px-2.5 py-1 text-xs font-medium text-white shadow-sm">
                    {{ $destination->distance_km }} km
                </span>
            @endisset
        </div>
    </a>

    @auth
        <form action="{{ $isFavorited ? route('favorites.destroy', $destination) : route('favorites.store', $destination) }}" method="POST" class="absolute top-3 right-3">
            @csrf
            @if ($isFavorited) @method('DELETE') @endif
            <button type="submit" title="{{ $isFavorited ? 'Hapus dari favorit' : 'Simpan ke favorit' }}" class="w-8 h-8 rounded-full bg-white/90 backdrop-blur flex items-center justify-center shadow-sm hover:bg-white transition {{ $isFavorited ? 'text-accent-500' : 'text-ink/40' }}">
                <svg class="w-4 h-4" fill="{{ $isFavorited ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
            </button>
        </form>
    @else
        <a href="{{ route('login') }}" title="Masuk untuk menyimpan favorit" class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/90 backdrop-blur flex items-center justify-center shadow-sm hover:bg-white transition text-ink/40">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
            </svg>
        </a>
    @endauth

    <a href="{{ route('destinations.show', $destination) }}" class="block p-4">
        <h3 class="font-semibold text-ink line-clamp-1">{{ $destination->name }}</h3>
        <p class="mt-1 text-sm text-ink/50 line-clamp-1">{{ $area }} &middot; {{ $destination->category->name }}</p>

        <div class="mt-3 flex items-center justify-between">
            <div class="flex items-center gap-1 text-sm text-ink/70">
                <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.447a1 1 0 00-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.538 1.118l-3.367-2.447a1 1 0 00-1.176 0l-3.367 2.447c-.783.57-1.838-.196-1.538-1.118l1.287-3.957a1 1 0 00-.363-1.118L2.062 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.957z" />
                </svg>
                <span>{{ $rating > 0 ? number_format($rating, 1) : 'Baru' }}</span>
            </div>
            <span class="text-sm font-semibold text-accent-500 group-hover:underline">Detail &rarr;</span>
        </div>
    </a>
</div>
