<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-accent-500">Itinerary</p>
                <h1 class="mt-1 text-3xl font-bold text-ink">Perjalananmu</h1>
                <p class="mt-1 text-sm text-ink/60">Kelola semua itinerary yang pernah kamu buat di sini.</p>
            </div>
            <div class="flex gap-2 shrink-0">
                <a href="{{ route('itineraries.ai-create') }}" class="inline-flex items-center gap-2 rounded-xl bg-accent-500 px-5 py-3 text-sm font-semibold text-white hover:bg-accent-600 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z" /></svg>
                    Susun dengan AI
                </a>
                <a href="{{ route('itineraries.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white hover:bg-primary-600 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Buat Manual
                </a>
            </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <div class="flex-1 flex items-center gap-2 border border-primary-100 rounded-xl px-4 py-3 bg-white">
                <svg class="w-4 h-4 text-ink/40 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input type="text" placeholder="Cari nama perjalanan..." class="flex-1 border-0 p-0 focus:ring-0 text-sm" disabled>
            </div>
            <select class="rounded-xl border-primary-100 px-4 py-3 text-sm bg-white text-ink/70" disabled>
                <option>Semua perjalanan</option>
            </select>
        </div>

        <h2 class="mt-8 font-semibold text-ink text-lg">
            {{ $itineraries->isEmpty() ? '' : 'Itinerary Aktif' }}
        </h2>

        @if ($itineraries->isEmpty())
            <div class="text-center py-16 bg-white rounded-2xl border border-primary-100">
                <p class="text-ink/60">Kamu belum punya itinerary. Yuk mulai susun rencana perjalananmu!</p>
                <a href="{{ route('itineraries.create') }}" class="mt-4 inline-flex items-center rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-600 transition">
                    Buat Itinerary Pertama
                </a>
            </div>
        @else
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($itineraries as $itinerary)
                    <div class="rounded-2xl border border-primary-100 bg-white overflow-hidden">
                        <div class="relative h-28 flex items-center justify-center {{ $loop->index % 2 === 0 ? 'bg-primary-50' : 'bg-accent-50' }}">
                            <span class="absolute top-3 left-3 inline-flex items-center rounded-full bg-white/90 backdrop-blur px-2.5 py-1 text-[10px] font-bold tracking-wide uppercase {{ $itinerary->isActive() ? 'text-primary-700' : 'text-ink/40' }}">
                                {{ $itinerary->isActive() ? 'Aktif' : 'Selesai' }}
                            </span>
                            <span class="absolute top-3 right-3 w-7 h-7 rounded-full bg-white/90 backdrop-blur flex items-center justify-center text-accent-500">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>
                            </span>
                            <span class="text-4xl">{{ $itinerary->emoji() }}</span>
                        </div>
                        <div class="p-4">
                            @if ($itinerary->user_id !== auth()->id())
                                <span class="inline-flex items-center gap-1 rounded-full bg-accent-100 text-accent-700 text-[10px] font-semibold px-2 py-0.5 mb-1.5">
                                    Kolaborasi &middot; {{ $itinerary->user->name }}
                                </span>
                            @endif
                            <h3 class="font-semibold text-ink line-clamp-1">{{ $itinerary->title }}</h3>
                            <p class="mt-0.5 text-xs text-ink/50">
                                {{ $itinerary->start_date->translatedFormat('d M Y') }} &mdash; {{ $itinerary->end_date->translatedFormat('d M Y') }}
                                &middot; {{ $itinerary->totalDays() }} Hari
                            </p>

                            <div class="mt-3">
                                <div class="h-1.5 bg-primary-50 rounded-full overflow-hidden">
                                    <div class="h-full bg-accent-400 rounded-full" style="width: {{ $itinerary->plannedProgress() }}%"></div>
                                </div>
                                <p class="mt-1.5 text-xs text-ink/50">{{ $itinerary->plannedProgress() }}% itinerary sudah direncanakan</p>
                            </div>

                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('itineraries.show', $itinerary) }}" class="flex-1 rounded-lg bg-primary text-white text-sm font-semibold py-2 text-center hover:bg-primary-600 transition">
                                    Buka Itinerary
                                </a>
                                <div x-data="{ open: false }" class="relative">
                                    <button type="button" @click="open = ! open" class="w-9 h-9 rounded-lg border border-primary-100 flex items-center justify-center text-ink/50 hover:bg-primary-50 transition">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4z" /></svg>
                                    </button>
                                    <div x-show="open" x-cloak @click.outside="open = false" class="absolute right-0 mt-1 w-40 rounded-xl border border-primary-100 bg-white shadow-lg py-1 z-10">
                                        @if ($itinerary->user_id === auth()->id())
                                            <a href="{{ route('itineraries.edit', $itinerary) }}" class="block px-4 py-2 text-sm text-ink/70 hover:bg-primary-50">Edit</a>
                                        @endif
                                        <a href="{{ route('itineraries.pdf', $itinerary) }}" class="block px-4 py-2 text-sm text-ink/70 hover:bg-primary-50">Download PDF</a>
                                        @if ($itinerary->user_id === auth()->id())
                                            <form action="{{ route('itineraries.destroy', $itinerary) }}" method="POST" onsubmit="return confirm('Hapus itinerary ini beserta seluruh jadwalnya?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50">Hapus</button>
                                            </form>
                                        @else
                                            <form action="{{ route('itinerary-collaborators.destroy', [$itinerary, auth()->user()]) }}" method="POST" onsubmit="return confirm('Keluar dari itinerary ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50">Keluar</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $itineraries->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
