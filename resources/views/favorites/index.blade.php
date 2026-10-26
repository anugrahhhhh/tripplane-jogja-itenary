<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-ink">Favorit Saya</h1>
        <p class="text-sm text-ink/60 mt-1">{{ $favorites->total() }} destinasi tersimpan.</p>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if ($favorites->isEmpty())
            <div class="text-center py-16 bg-white rounded-2xl border border-primary-100">
                <p class="text-ink/60">Kamu belum menyimpan destinasi favorit.</p>
                <a href="{{ route('destinations.index') }}" class="mt-4 inline-flex items-center rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-600 transition">
                    Jelajahi Katalog
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($favorites as $favorite)
                    <div class="relative">
                        <x-destination-card :destination="$favorite->destination" />
                        <form action="{{ route('favorites.destroy', $favorite->destination) }}" method="POST" class="absolute top-3 right-3">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Hapus dari favorit" class="w-8 h-8 rounded-full bg-white/90 backdrop-blur flex items-center justify-center text-red-500 hover:bg-white shadow-sm">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $favorites->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
