<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="text-sm text-ink/50 mb-6">
            <a href="{{ route('destinations.index') }}" class="hover:text-primary">Katalog</a>
            <span class="mx-1">/</span>
            <span class="font-semibold text-ink">{{ $activeCategory->name }}</span>
        </nav>

        <div class="flex items-center justify-between flex-wrap gap-3 mb-6">
            <div class="flex items-center gap-3">
                <span class="w-11 h-11 rounded-xl bg-primary-50 flex items-center justify-center">
                    <x-category-icon :slug="$activeCategory->slug" class="w-5 h-5 text-primary-600" />
                </span>
                <div>
                    <h1 class="text-2xl font-bold text-ink">{{ $activeCategory->name }}</h1>
                    <p class="text-sm text-ink/60">{{ $destinations->total() }} destinasi</p>
                </div>
            </div>

            <form action="{{ route('destinations.by-category', $activeCategory) }}" method="GET" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari di kategori ini..." class="rounded-lg border-primary-100 text-sm focus:border-primary focus:ring-primary">
                <select name="urutkan" onchange="this.form.submit()" class="rounded-lg border-primary-100 text-sm focus:border-primary focus:ring-primary">
                    <option value="rating" @selected($sort === 'rating')>Rating Tertinggi</option>
                    <option value="terbaru" @selected($sort === 'terbaru')>Terbaru</option>
                    <option value="jarak" @selected($sort === 'jarak')>Jarak Terdekat</option>
                </select>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @forelse ($destinations as $destination)
                <x-destination-card :destination="$destination" />
            @empty
                <div class="col-span-full text-center py-16">
                    <p class="text-ink/50">Belum ada destinasi di kategori ini.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $destinations->links() }}
        </div>
    </div>
</x-app-layout>
