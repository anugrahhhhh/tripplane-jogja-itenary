<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <p class="text-xs font-semibold tracking-wide uppercase text-accent-500">Explore Yogyakarta</p>
        <h1 class="mt-1 text-3xl sm:text-4xl font-bold text-ink">Katalog Destinasi</h1>
        <p class="mt-2 text-sm text-ink/60 max-w-xl">Temukan tempat menarik berdasarkan kategori, lalu pilih destinasi untuk melihat informasi lebih lengkap.</p>

        <div x-data="{ q: {{ \Illuminate\Support\Js::from(request('q', '')) }}, filterOpen: false }" class="mt-6">
            <div class="flex flex-col sm:flex-row gap-3">
                <form action="{{ route('destinations.index') }}" method="GET" class="flex-1 flex items-center gap-2 bg-accent-50 rounded-xl px-4 py-3">
                    <svg class="w-4 h-4 text-ink/40 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="q" x-model="q" placeholder="Cari nama tempat, restoran, atau destinasi..." class="flex-1 border-0 bg-transparent p-0 focus:ring-0 text-sm">
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-accent-500 px-5 py-2 text-sm font-semibold text-white hover:bg-accent-600 transition shrink-0">
                        Cari
                    </button>
                </form>

                <div class="relative shrink-0">
                    <button type="button" @click="filterOpen = ! filterOpen" class="inline-flex items-center gap-2 rounded-xl border border-primary-100 bg-white px-4 py-3 text-sm font-medium text-ink/70 hover:bg-primary-50 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m9 12h3.75M10.5 18a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 18H7.5m9-6h3.75M13.5 12a1.5 1.5 0 10-3 0m3 0a1.5 1.5 0 11-3 0M3.75 12H10.5" /></svg>
                        Filter
                        @if (request('kategori') || request('urutkan'))
                            <span class="w-1.5 h-1.5 rounded-full bg-accent-500"></span>
                        @endif
                    </button>
                    <div x-show="filterOpen" x-cloak @click.outside="filterOpen = false" class="absolute right-0 mt-2 w-56 rounded-xl border border-primary-100 bg-white shadow-lg p-3 z-20">
                        <form action="{{ route('destinations.index') }}" method="GET">
                            <input type="hidden" name="q" x-model="q">
                            <label class="block text-xs font-medium text-ink/50 uppercase mb-1">Kategori</label>
                            <select name="kategori" class="w-full rounded-lg border-primary-100 text-sm focus:border-primary focus:ring-primary">
                                <option value="">Semua Kategori</option>
                                @foreach ($allCategories as $category)
                                    <option value="{{ $category->slug }}" @selected(request('kategori') === $category->slug)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <label class="block text-xs font-medium text-ink/50 uppercase mt-3 mb-1">Urutkan</label>
                            <select name="urutkan" class="w-full rounded-lg border-primary-100 text-sm focus:border-primary focus:ring-primary">
                                <option value="rating" @selected(request('urutkan', 'rating') === 'rating')>Rating Tertinggi</option>
                                <option value="terbaru" @selected(request('urutkan') === 'terbaru')>Terbaru</option>
                                <option value="jarak" @selected(request('urutkan') === 'jarak')>Jarak Terdekat</option>
                            </select>
                            <button type="submit" class="mt-3 w-full rounded-lg bg-primary text-white text-sm font-semibold py-2 hover:bg-primary-600 transition">Terapkan</button>
                        </form>
                    </div>
                </div>
            </div>

            @if (request('kategori') || request('urutkan'))
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="text-xs text-ink/40">Filter aktif:</span>
                    @if (request('kategori'))
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-primary-50 text-primary-700 text-xs font-medium px-3 py-1">
                            {{ $allCategories->firstWhere('slug', request('kategori'))?->name ?? request('kategori') }}
                        </span>
                    @endif
                    @if (request('urutkan') && request('urutkan') !== 'rating')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-primary-50 text-primary-700 text-xs font-medium px-3 py-1">
                            Urut: {{ request('urutkan') === 'terbaru' ? 'Terbaru' : 'Jarak Terdekat' }}
                        </span>
                    @endif
                    <a href="{{ route('destinations.index', array_filter(['q' => request('q')])) }}" class="text-xs font-semibold text-accent-500 hover:underline">
                        Hapus filter &times;
                    </a>
                </div>
            @endif
        </div>

        @if ($mode === 'browse')
            <div class="mt-10 space-y-8">
                @foreach ($categoryGroups as $category)
                    @if ($category->destinations->isNotEmpty())
                        <div class="rounded-3xl border border-primary-100 bg-white p-6">
                            <div class="flex items-center justify-between">
                                <h2 class="font-semibold text-ink flex items-center gap-2">
                                    <x-category-icon :slug="$category->slug" class="w-5 h-5 text-primary-600" />
                                    {{ $category->name }}
                                </h2>
                                <a href="{{ route('destinations.by-category', $category) }}" class="text-sm font-semibold text-accent-500 hover:underline shrink-0">
                                    Lainnya &rarr;
                                </a>
                            </div>
                            <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4">
                                @foreach ($category->destinations as $destination)
                                    <x-destination-card :destination="$destination" />
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="mt-8 flex flex-wrap gap-2">
                <a href="{{ route('destinations.index') }}" class="inline-flex items-center rounded-full px-3.5 py-1.5 text-xs font-medium bg-white border border-primary-100 text-primary-700 hover:bg-primary-50 transition">
                    &larr; Kembali jelajahi kategori
                </a>
            </div>

            <p class="mt-4 text-sm text-ink/60">{{ $destinations->total() }} destinasi ditemukan.</p>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($destinations as $destination)
                    <x-destination-card :destination="$destination" />
                @empty
                    <div class="col-span-full text-center py-16">
                        <p class="text-ink/50">Tidak ada destinasi yang cocok dengan pencarianmu.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-10">
                {{ $destinations->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
