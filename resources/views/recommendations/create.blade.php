<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-ink">Rekomendasi AI</h1>
        <p class="text-sm text-ink/60 mt-1">Ceritakan preferensimu, biar AI pilihkan destinasi yang paling cocok di Kota Yogyakarta.</p>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('error'))
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('recommendations.store') }}" method="POST" x-data="aiRecommendationForm()" class="bg-white rounded-2xl border border-primary-100 p-6 space-y-6">
            @csrf
            <input type="hidden" name="lat" :value="lat">
            <input type="hidden" name="lng" :value="lng">

            <div>
                <label class="block text-sm font-semibold text-ink mb-2">Minat kategori (boleh pilih lebih dari satu)</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach ($categories as $category)
                        <label class="flex items-center gap-2 rounded-lg border border-primary-100 px-3 py-2 text-sm cursor-pointer hover:bg-primary-50 transition has-[:checked]:bg-primary-50 has-[:checked]:border-primary-300">
                            <input type="checkbox" name="interests[]" value="{{ $category->name }}" class="rounded border-primary-300 text-primary focus:ring-primary" @checked(in_array($category->name, old('interests', [])))>
                            <span class="text-ink/80">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('interests') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="notes" class="block text-sm font-semibold text-ink mb-2">Ceritakan lebih detail (opsional)</label>
                <textarea id="notes" name="notes" rows="3" maxlength="500" placeholder="Contoh: suka suasana tenang, cari yang ramah untuk anak kecil, ingin coba kuliner malam yang unik..."
                    class="w-full rounded-lg border-primary-100 text-sm focus:border-primary focus:ring-primary">{{ old('notes') }}</textarea>
                @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="budget_max" class="block text-sm font-semibold text-ink mb-2">Budget maksimal per aktivitas (Rp)</label>
                    <input type="number" id="budget_max" name="budget_max" min="0" step="1000" value="{{ old('budget_max') }}" placeholder="Contoh: 50000"
                        class="w-full rounded-lg border-primary-100 text-sm focus:border-primary focus:ring-primary">
                    @error('budget_max') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="duration" class="block text-sm font-semibold text-ink mb-2">Durasi kunjungan</label>
                    <select id="duration" name="duration" class="w-full rounded-lg border-primary-100 text-sm focus:border-primary focus:ring-primary">
                        <option value="">Tidak ditentukan</option>
                        <option value="singkat" @selected(old('duration') === 'singkat')>1-2 jam</option>
                        <option value="setengah-hari" @selected(old('duration') === 'setengah-hari')>Setengah hari (3-5 jam)</option>
                        <option value="seharian" @selected(old('duration') === 'seharian')>Seharian (6+ jam)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-ink mb-2">Lokasi saat ini (opsional, untuk pertimbangan jarak)</label>
                <button type="button" @click="locate()" class="inline-flex items-center gap-2 rounded-lg border border-primary-200 bg-white px-4 py-2.5 text-sm font-medium text-primary-700 hover:bg-primary-50 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v3m0 14v3M2 12h3m14 0h3" /></svg>
                    <span x-text="locating ? 'Mencari lokasimu...' : (lat ? 'Lokasi berhasil dibagikan' : 'Bagikan Lokasi Saya')"></span>
                </button>
                <p class="mt-2 text-xs text-red-600" x-show="locError" x-text="locError"></p>
                <p class="mt-2 text-xs text-ink/50" x-show="lat">Titik: <span x-text="lat + ', ' + lng"></span></p>
            </div>

            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white hover:bg-primary-600 transition">
                Buatkan Rekomendasi
            </button>
        </form>
    </div>

    <x-slot name="head">
        <script>
            function aiRecommendationForm() {
                return {
                    lat: null,
                    lng: null,
                    locating: false,
                    locError: null,
                    locate() {
                        if (! navigator.geolocation) {
                            this.locError = 'Browser tidak mendukung geolokasi.';
                            return;
                        }
                        this.locating = true;
                        this.locError = null;
                        navigator.geolocation.getCurrentPosition((position) => {
                            this.lat = position.coords.latitude;
                            this.lng = position.coords.longitude;
                            this.locating = false;
                        }, () => {
                            this.locating = false;
                            this.locError = 'Izin lokasi ditolak. Rekomendasi tetap bisa dibuat tanpa lokasi.';
                        });
                    },
                };
            }
        </script>
    </x-slot>
</x-app-layout>
