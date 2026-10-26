<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-ink">Rekomendasi Terdekat</h1>
        <p class="text-sm text-ink/60 mt-1">Aktifkan lokasi, atau klik titik di peta untuk memilih posisi manual.</p>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="nearbyPage()" x-init="init()">
        <div class="flex flex-wrap items-center gap-3">
            <button type="button" @click="locateMe()" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-600 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v3m0 14v3M2 12h3m14 0h3" /></svg>
                <span x-text="loading ? 'Mencari lokasimu...' : 'Aktifkan Lokasi Saya'"></span>
            </button>

            <div class="inline-flex rounded-lg border border-primary-200 overflow-hidden">
                <template x-for="r in [1, 3, 5]" :key="r">
                    <button type="button" @click="radius = r; search();"
                            :class="radius === r ? 'bg-primary text-white' : 'bg-white text-primary-700 hover:bg-primary-50'"
                            class="px-4 py-2 text-sm font-medium transition" x-text="r + ' km'"></button>
                </template>
            </div>

            <p class="text-xs text-ink/50" x-show="point">
                Titik terpilih: <span x-text="point ? point.lat.toFixed(5) + ', ' + point.lng.toFixed(5) : ''"></span>
            </p>
        </div>

        <p class="mt-3 text-sm text-red-600" x-show="error" x-text="error"></p>
        <p class="mt-3 text-sm text-ink/50" x-show="!point">Belum ada titik lokasi dipilih. Klik tombol di atas atau klik langsung di peta.</p>

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div id="nearby-map" class="h-96 rounded-2xl border border-primary-100 z-0"></div>
            </div>

            <div class="lg:col-span-2">
                <form :action="'{{ route('today-itinerary.store') }}'" method="POST" @submit="submitToday">
                    @csrf
                    <input type="hidden" name="lat" :value="point ? point.lat : ''">
                    <input type="hidden" name="lng" :value="point ? point.lng : ''">
                    <template x-if="items.length > 0">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm text-ink/60"><span x-text="items.length"></span> destinasi ditemukan dalam radius <span x-text="radius"></span> km.</p>
                            @auth
                                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2 text-xs font-semibold text-primary-800 hover:bg-accent-300 transition">
                                    Tambahkan ke Itinerary Hari Ini
                                </button>
                            @endauth
                        </div>
                    </template>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <template x-for="item in items" :key="item.id">
                            <label class="relative block bg-white border border-primary-100 rounded-2xl p-4 cursor-pointer hover:shadow-md transition">
                                @auth
                                    <input type="checkbox" name="destination_ids[]" :value="item.id" class="absolute top-3 right-3 w-4 h-4 rounded border-primary-300 text-primary focus:ring-primary">
                                @endauth
                                <p class="text-xs font-medium text-primary-600" x-text="item.category"></p>
                                <h3 class="mt-1 font-semibold text-ink pr-6" x-text="item.name"></h3>
                                <div class="mt-2 flex items-center gap-2 text-xs text-ink/50">
                                    <span x-text="item.distance_km !== null ? item.distance_km + ' km' : 'Jarak segera tersedia'"></span>
                                    <span x-show="item.is_open !== null" :class="item.is_open ? 'text-primary-600' : 'text-red-500'" x-text="item.is_open ? 'Buka sekarang' : 'Tutup sekarang'"></span>
                                </div>
                                <a :href="'/destinasi/' + item.slug" class="mt-2 inline-block text-xs font-semibold text-primary-700 hover:underline">Lihat detail &rarr;</a>
                            </label>
                        </template>
                    </div>

                    <template x-if="!loading && point && items.length === 0">
                        <p class="text-sm text-ink/50 mt-4">Tidak ada destinasi ditemukan di titik ini.</p>
                    </template>
                </form>
            </div>
        </div>
    </div>

    <x-slot name="head">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
        <script>
            function nearbyPage() {
                return {
                    map: null,
                    userMarker: null,
                    itemsLayer: null,
                    point: null,
                    radius: 3,
                    loading: false,
                    error: null,
                    items: [],
                    init() {
                        this.map = L.map('nearby-map').setView([-7.7972, 110.3688], 13);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors',
                        }).addTo(this.map);
                        this.itemsLayer = L.layerGroup().addTo(this.map);

                        this.map.on('click', (e) => {
                            this.setPoint(e.latlng.lat, e.latlng.lng);
                        });
                    },
                    locateMe() {
                        if (! navigator.geolocation) {
                            this.error = 'Browser tidak mendukung geolokasi.';
                            return;
                        }
                        this.loading = true;
                        this.error = null;
                        navigator.geolocation.getCurrentPosition((position) => {
                            this.setPoint(position.coords.latitude, position.coords.longitude);
                            this.map.setView([position.coords.latitude, position.coords.longitude], 14);
                        }, () => {
                            this.loading = false;
                            this.error = 'Izin lokasi ditolak. Silakan klik titik di peta untuk memilih lokasi manual.';
                        });
                    },
                    setPoint(lat, lng) {
                        this.point = { lat, lng };
                        if (this.userMarker) {
                            this.map.removeLayer(this.userMarker);
                        }
                        this.userMarker = L.marker([lat, lng], {
                            icon: L.divIcon({ className: '', html: '<div style="width:14px;height:14px;background:#2F6B5A;border:2px solid white;border-radius:50%;"></div>' }),
                        }).addTo(this.map);
                        this.search();
                    },
                    search() {
                        if (! this.point) {
                            return;
                        }
                        this.loading = true;
                        const params = new URLSearchParams({ lat: this.point.lat, lng: this.point.lng, radius: this.radius });
                        fetch(`{{ route('nearby.search') }}?${params}`, { headers: { 'Accept': 'application/json' } })
                            .then((res) => res.json())
                            .then((json) => {
                                this.items = json.data || [];
                                this.loading = false;
                                this.renderMarkers();
                            })
                            .catch(() => { this.loading = false; });
                    },
                    renderMarkers() {
                        this.itemsLayer.clearLayers();
                        this.items.forEach((item) => {
                            L.marker([item.latitude, item.longitude]).bindPopup(item.name).addTo(this.itemsLayer);
                        });
                    },
                    submitToday(e) {
                        const checked = this.$el.querySelectorAll('input[name="destination_ids[]"]:checked');
                        if (checked.length === 0) {
                            e.preventDefault();
                            alert('Pilih minimal satu destinasi terlebih dahulu.');
                        }
                    },
                };
            }
        </script>
    </x-slot>
</x-app-layout>
