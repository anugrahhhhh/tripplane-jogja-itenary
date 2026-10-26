<x-app-layout>
    <!-- Welcome bar -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        <h1 class="text-xl font-semibold text-ink">Welcome to Yogyakarta 👋</h1>
        <p class="mt-1 text-sm text-ink/60">Temukan destinasi menarik dan rencanakan perjalananmu di Yogyakarta.</p>
    </div>

    <!-- Hero -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div class="rounded-3xl bg-accent-50 px-6 py-10 sm:px-12 sm:py-16 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div>
                <h2 class="text-4xl sm:text-5xl font-bold text-ink leading-tight text-balance">
                    Rencanakan perjalananmu di <span class="text-accent-500">Yogyakarta</span>
                </h2>
                <p class="mt-4 text-ink/70 max-w-md">
                    Temukan destinasi terbaik, simpan favorit, dan susun itinerary lengkap tanpa perlu bolak-balik Instagram, TikTok, atau Google.
                </p>

                <form action="{{ route('destinations.index') }}" method="GET" class="mt-6 flex items-center gap-2 bg-white rounded-xl shadow-sm p-2 max-w-md">
                    <svg class="w-5 h-5 text-ink/40 ms-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="q" placeholder="Search destination..." class="flex-1 border-0 focus:ring-0 text-sm text-ink placeholder:text-ink/40">
                    <button type="submit" class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-600 transition shrink-0">
                        Cari
                    </button>
                </form>
            </div>

            <div class="relative aspect-[4/3] rounded-2xl overflow-hidden bg-gradient-to-br from-primary-700 via-primary to-accent-300">
                <svg class="absolute inset-0 w-full h-full opacity-20" viewBox="0 0 400 300" fill="none">
                    <circle cx="60" cy="240" r="140" stroke="white" stroke-width="1.5"/>
                    <circle cx="360" cy="40" r="100" stroke="white" stroke-width="1.5"/>
                    <circle cx="340" cy="260" r="60" stroke="white" stroke-width="1.5"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-3xl sm:text-4xl font-bold text-white/90">Jogja<span class="text-accent-100">Trip</span></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Di Sekitarmu -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="nearbyPreview()">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="text-2xl font-bold text-ink">Di Sekitarmu</h2>
                <p class="text-sm text-ink/60 mt-1">Aktifkan lokasi untuk melihat destinasi terdekat dari posisimu sekarang.</p>
            </div>
            <button @click="locate()" type="button" class="inline-flex items-center gap-2 rounded-lg border border-primary-200 bg-white px-4 py-2 text-sm font-semibold text-primary-700 hover:bg-primary-50 transition shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v3m0 14v3M2 12h3m14 0h3" />
                </svg>
                <span x-text="loading ? 'Mencari lokasimu...' : 'Aktifkan Lokasi Saya'"></span>
            </button>
        </div>

        <div class="mt-6">
            <template x-if="error">
                <p class="text-sm text-red-600" x-text="error"></p>
            </template>

            <template x-if="!started">
                <p class="text-sm text-ink/50">Klik &quot;Aktifkan Lokasi Saya&quot; untuk melihat rekomendasi di sekitarmu, atau jelajahi lewat <a href="{{ route('nearby.index') }}" class="text-accent-500 font-medium underline">halaman Rekomendasi Terdekat</a>.</p>
            </template>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5" x-show="items.length > 0">
                <template x-for="item in items" :key="item.id">
                    <a :href="'/destinasi/' + item.slug" class="block bg-white rounded-2xl border border-primary-100 p-4 hover:shadow-md transition">
                        <p class="text-xs font-medium text-primary-600" x-text="item.category"></p>
                        <h3 class="mt-1 font-semibold text-ink" x-text="item.name"></h3>
                        <p class="mt-2 text-xs text-ink/50" x-text="item.distance_km !== null ? item.distance_km + ' km dari lokasimu' : 'Jarak segera tersedia'"></p>
                    </a>
                </template>
            </div>
        </div>
    </section>

    <!-- Rekomendasi -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-ink">Recommended for You</h2>
            <a href="{{ route('destinations.index') }}" class="text-sm font-semibold text-accent-500 hover:underline">Lihat semua &rarr;</a>
        </div>

        <div class="mt-6 flex gap-5 overflow-x-auto pb-2 snap-x snap-mandatory sm:grid sm:grid-cols-2 lg:grid-cols-4 sm:overflow-visible">
            @forelse ($popular as $destination)
                <div class="w-64 shrink-0 snap-start sm:w-auto">
                    <x-destination-card :destination="$destination" />
                </div>
            @empty
                <p class="text-sm text-ink/50 col-span-full">Belum ada destinasi yang bisa ditampilkan.</p>
            @endforelse
        </div>
    </section>

    <x-slot name="head">
        <script>
            function nearbyPreview() {
                return {
                    loading: false,
                    started: false,
                    error: null,
                    items: [],
                    locate() {
                        if (! navigator.geolocation) {
                            this.error = 'Browser tidak mendukung geolokasi.';
                            return;
                        }

                        this.loading = true;
                        this.error = null;

                        navigator.geolocation.getCurrentPosition((position) => {
                            this.started = true;
                            this.loading = false;
                            const params = new URLSearchParams({
                                lat: position.coords.latitude,
                                lng: position.coords.longitude,
                                radius: 5,
                            });

                            fetch(`{{ route('nearby.search') }}?${params}`, {
                                headers: { 'Accept': 'application/json' },
                            })
                                .then((res) => res.json())
                                .then((json) => {
                                    this.items = (json.data || []).slice(0, 4);
                                });
                        }, () => {
                            this.loading = false;
                            this.started = true;
                            this.error = 'Izin lokasi ditolak atau tidak tersedia. Coba lewat halaman Rekomendasi Terdekat untuk memilih titik manual di peta.';
                        });
                    },
                };
            }
        </script>
    </x-slot>
</x-app-layout>
