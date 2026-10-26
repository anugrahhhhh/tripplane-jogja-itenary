<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-ink">Susun Itinerary dengan AI</h1>
        <p class="text-sm text-ink/60 mt-1">Kasih tahu preferensimu, AI akan susunkan itinerary multi-hari lengkap dengan urutan dan jam kunjungan.</p>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('error'))
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('itineraries.ai-store') }}" method="POST" x-data="aiItineraryForm()" class="bg-white rounded-2xl border border-primary-100 p-6 space-y-6">
            @csrf
            <input type="hidden" name="lat" :value="lat">
            <input type="hidden" name="lng" :value="lng">

            <div>
                <x-input-label for="title" value="Nama Itinerary (opsional)" />
                <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title') }}" placeholder="Contoh: Liburan Jogja 3 Hari" />
                <x-input-error :messages="$errors->get('title')" class="mt-1" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="start_date" value="Tanggal Mulai" />
                    <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" value="{{ old('start_date') }}" required />
                    <x-input-error :messages="$errors->get('start_date')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="end_date" value="Tanggal Selesai" />
                    <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" value="{{ old('end_date') }}" required />
                    <x-input-error :messages="$errors->get('end_date')" class="mt-1" />
                </div>
            </div>
            <p class="text-xs text-ink/40 -mt-3">Maksimal 7 hari per itinerary buatan AI.</p>

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
            </div>

            <div>
                <x-input-label for="notes" value="Ceritakan lebih detail (opsional)" />
                <textarea id="notes" name="notes" rows="3" maxlength="500" placeholder="Contoh: perjalanan santai bareng keluarga, hindari tempat yang terlalu ramai, suka wisata sejarah..."
                    class="mt-1 w-full rounded-lg border-primary-100 text-sm focus:border-primary focus:ring-primary">{{ old('notes') }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="budget_per_day" value="Budget per hari (Rp, opsional)" />
                <x-text-input id="budget_per_day" name="budget_per_day" type="number" min="0" step="1000" class="mt-1 block w-full" value="{{ old('budget_per_day') }}" placeholder="Contoh: 300000" />
                <x-input-error :messages="$errors->get('budget_per_day')" class="mt-1" />
            </div>

            <div>
                <label class="block text-sm font-semibold text-ink mb-2">Titik keberangkatan (opsional, untuk pertimbangan jarak)</label>
                <button type="button" @click="locate()" class="inline-flex items-center gap-2 rounded-lg border border-primary-200 bg-white px-4 py-2.5 text-sm font-medium text-primary-700 hover:bg-primary-50 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v3m0 14v3M2 12h3m14 0h3" /></svg>
                    <span x-text="locating ? 'Mencari lokasimu...' : (lat ? 'Lokasi berhasil dibagikan' : 'Bagikan Lokasi Saya')"></span>
                </button>
                <p class="mt-2 text-xs text-red-600" x-show="locError" x-text="locError"></p>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('itineraries.index') }}" class="inline-flex items-center rounded-lg border border-primary-200 px-4 py-2.5 text-sm font-semibold text-primary-700 hover:bg-primary-50 transition">Batal</a>
                <button type="submit" :disabled="submitting" @click="submitting = true"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-600 transition disabled:opacity-60">
                    <span x-show="!submitting">Susunkan Itinerary</span>
                    <span x-show="submitting">Menyusun itinerary...</span>
                </button>
            </div>
        </form>
    </div>

    <x-slot name="head">
        <script>
            function aiItineraryForm() {
                return {
                    lat: null,
                    lng: null,
                    locating: false,
                    locError: null,
                    submitting: false,
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
                            this.locError = 'Izin lokasi ditolak. Itinerary tetap bisa disusun tanpa lokasi.';
                        });
                    },
                };
            }
        </script>
    </x-slot>
</x-app-layout>
