<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-ink">Rekomendasi Untukmu</h1>
        <p class="text-sm text-ink/60 mt-1">Dipilih oleh AI berdasarkan preferensi yang kamu berikan.</p>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <a href="{{ route('recommendations.create') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary-700 hover:underline">
            &larr; Ubah preferensi
        </a>

        @if (empty($recommendations))
            <div class="mt-6 text-center py-16 bg-white rounded-2xl border border-primary-100">
                <p class="text-ink/60">AI tidak menemukan destinasi yang cocok. Coba ubah preferensimu.</p>
            </div>
        @else
            @auth
                <form action="{{ route('today-itinerary.store') }}" method="POST" x-data="{ selected: [] }" class="mt-6">
                    @csrf
                    <input type="hidden" name="lat" value="{{ $lat }}">
                    <input type="hidden" name="lng" value="{{ $lng }}">

                    <div class="flex items-center justify-between rounded-xl border border-primary-100 bg-white px-4 py-3 mb-5">
                        <p class="text-sm text-ink/60">
                            <span x-text="selected.length"></span> destinasi dipilih
                        </p>
                        <button type="submit" :disabled="selected.length === 0"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-600 transition disabled:opacity-40 disabled:cursor-not-allowed">
                            Tambahkan ke Itinerary Hari Ini
                        </button>
                    </div>

                    <div class="space-y-5">
                        @foreach ($recommendations as $item)
                            @php $destination = $item['destination']; @endphp
                            <label class="block bg-white rounded-2xl border border-primary-100 overflow-hidden sm:flex cursor-pointer has-[:checked]:ring-2 has-[:checked]:ring-primary-300">
                                <div class="relative block sm:w-56 shrink-0 aspect-[4/3] sm:aspect-auto bg-primary-50">
                                    <input type="checkbox" name="destination_ids[]" value="{{ $destination->id }}" x-model="selected"
                                        class="absolute top-3 left-3 w-5 h-5 rounded border-primary-300 text-primary focus:ring-primary z-10">
                                    @if ($destination->cover_photo)
                                        <img src="{{ asset('storage/'.$destination->cover_photo) }}" alt="{{ $destination->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-primary-300">
                                            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 21h18a1.5 1.5 0 001.5-1.5V4.5A1.5 1.5 0 0021 3H3a1.5 1.5 0 00-1.5 1.5v15A1.5 1.5 0 003 21z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <div class="p-5 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <span class="inline-flex items-center rounded-full bg-primary-50 text-primary-700 text-xs font-semibold px-2.5 py-1">
                                                {{ $loop->iteration }}. {{ $destination->category->name }}
                                            </span>
                                            <h3 class="mt-2 font-semibold text-ink text-lg">{{ $destination->name }}</h3>
                                        </div>
                                        @if (isset($destination->distance_km))
                                            <span class="text-xs text-ink/50 shrink-0">{{ $destination->distance_km }} km</span>
                                        @endif
                                    </div>

                                    <p class="mt-1 text-sm text-ink/50">{{ \Illuminate\Support\Str::before($destination->address, ',') }}</p>

                                    <div class="mt-3 rounded-xl bg-accent-50 border border-accent-100 px-4 py-3">
                                        <p class="text-xs font-semibold text-accent-600 uppercase tracking-wide mb-1">Kenapa cocok untukmu</p>
                                        <p class="text-sm text-ink/80">{{ $item['reason'] }}</p>
                                    </div>

                                    <a href="{{ route('destinations.show', $destination) }}" onclick="event.stopPropagation()" class="mt-3 inline-block text-sm font-semibold text-accent-500 hover:underline">
                                        Lihat detail &rarr;
                                    </a>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </form>
            @else
                <p class="mt-6 text-sm text-ink/60 bg-accent-50 border border-accent-100 rounded-xl px-4 py-3">
                    <a href="{{ route('login') }}" class="font-semibold text-accent-600 hover:underline">Masuk</a> untuk bisa langsung menambahkan destinasi ini ke Itinerary Hari Ini.
                </p>

                <div class="mt-6 space-y-5">
                    @foreach ($recommendations as $item)
                        @php $destination = $item['destination']; @endphp
                        <div class="bg-white rounded-2xl border border-primary-100 overflow-hidden sm:flex">
                            <a href="{{ route('destinations.show', $destination) }}" class="block sm:w-56 shrink-0 aspect-[4/3] sm:aspect-auto bg-primary-50">
                                @if ($destination->cover_photo)
                                    <img src="{{ asset('storage/'.$destination->cover_photo) }}" alt="{{ $destination->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-primary-300">
                                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 21h18a1.5 1.5 0 001.5-1.5V4.5A1.5 1.5 0 0021 3H3a1.5 1.5 0 00-1.5 1.5v15A1.5 1.5 0 003 21z" />
                                        </svg>
                                    </div>
                                @endif
                            </a>

                            <div class="p-5 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <span class="inline-flex items-center rounded-full bg-primary-50 text-primary-700 text-xs font-semibold px-2.5 py-1">
                                            {{ $loop->iteration }}. {{ $destination->category->name }}
                                        </span>
                                        <h3 class="mt-2 font-semibold text-ink text-lg">{{ $destination->name }}</h3>
                                    </div>
                                    @if (isset($destination->distance_km))
                                        <span class="text-xs text-ink/50 shrink-0">{{ $destination->distance_km }} km</span>
                                    @endif
                                </div>

                                <p class="mt-1 text-sm text-ink/50">{{ \Illuminate\Support\Str::before($destination->address, ',') }}</p>

                                <div class="mt-3 rounded-xl bg-accent-50 border border-accent-100 px-4 py-3">
                                    <p class="text-xs font-semibold text-accent-600 uppercase tracking-wide mb-1">Kenapa cocok untukmu</p>
                                    <p class="text-sm text-ink/80">{{ $item['reason'] }}</p>
                                </div>

                                <a href="{{ route('destinations.show', $destination) }}" class="mt-3 inline-block text-sm font-semibold text-accent-500 hover:underline">
                                    Lihat detail &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endauth
        @endif
    </div>
</x-app-layout>
