<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-ink">Dashboard Admin</h1>
        <p class="text-sm text-ink/60 mt-1">Ringkasan statistik JogjaTrip.</p>
    </x-slot>

    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-primary-100 rounded-2xl p-5">
            <p class="text-xs text-ink/50">Total Destinasi</p>
            <p class="mt-1 text-2xl font-bold text-primary-700">{{ $totalDestinations }}</p>
        </div>
        <div class="bg-white border border-primary-100 rounded-2xl p-5">
            <p class="text-xs text-ink/50">Total Kategori</p>
            <p class="mt-1 text-2xl font-bold text-primary-700">{{ $totalCategories }}</p>
        </div>
        <div class="bg-white border border-primary-100 rounded-2xl p-5">
            <p class="text-xs text-ink/50">Traveler Terdaftar</p>
            <p class="mt-1 text-2xl font-bold text-primary-700">{{ $totalTravelers }}</p>
        </div>
        <div class="bg-white border border-primary-100 rounded-2xl p-5">
            <p class="text-xs text-ink/50">Total Itinerary</p>
            <p class="mt-1 text-2xl font-bold text-primary-700">{{ $totalItineraries }}</p>
        </div>
        <div class="bg-white border border-primary-100 rounded-2xl p-5">
            <p class="text-xs text-ink/50">Total Ulasan</p>
            <p class="mt-1 text-2xl font-bold text-primary-700">{{ $totalReviews }}</p>
        </div>
        <div class="bg-accent-50 border border-accent-200 rounded-2xl p-5">
            <p class="text-xs text-primary-700">Ulasan Disembunyikan</p>
            <p class="mt-1 text-2xl font-bold text-primary-800">{{ $pendingReviews }}</p>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white border border-primary-100 rounded-2xl p-5">
            <h2 class="font-semibold text-ink">Destinasi per Kategori</h2>
            <ul class="mt-3 space-y-2">
                @foreach ($destinationsPerCategory as $category)
                    <li class="flex items-center justify-between text-sm">
                        <span class="text-ink/70">{{ $category->name }}</span>
                        <span class="font-semibold text-primary-700">{{ $category->destinations_count }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="bg-white border border-primary-100 rounded-2xl p-5">
            <h2 class="font-semibold text-ink">Ulasan Terbaru</h2>
            <ul class="mt-3 space-y-3">
                @forelse ($latestReviews as $review)
                    <li class="text-sm">
                        <p class="font-medium text-ink">{{ $review->user->name }} &rarr; {{ $review->destination->name }}</p>
                        <p class="text-ink/50 text-xs">Rating {{ $review->rating }}/5 &middot; {{ $review->created_at->diffForHumans() }}</p>
                    </li>
                @empty
                    <li class="text-sm text-ink/50">Belum ada ulasan.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <h2 class="mt-10 font-semibold text-ink text-lg">Analitik</h2>
    <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white border border-primary-100 rounded-2xl p-5">
            <h3 class="font-semibold text-ink text-sm">Kategori Terpopuler</h3>
            <p class="text-xs text-ink/50">Berdasarkan total favorit destinasi di kategori itu.</p>
            @if ($categoryPopularity->sum('favorites_count') > 0)
                <div class="mt-4 h-64"><canvas id="chart-category-popularity"></canvas></div>
            @else
                <p class="mt-6 text-sm text-ink/40 text-center py-10">Belum ada data favorit.</p>
            @endif
        </div>

        <div class="bg-white border border-primary-100 rounded-2xl p-5">
            <h3 class="font-semibold text-ink text-sm">Destinasi Paling Difavoritkan</h3>
            <p class="text-xs text-ink/50">Top {{ $topFavoritedDestinations->count() }} destinasi berdasarkan jumlah favorit.</p>
            @if ($topFavoritedDestinations->isNotEmpty())
                <div class="mt-4 h-64"><canvas id="chart-top-favorited"></canvas></div>
            @else
                <p class="mt-6 text-sm text-ink/40 text-center py-10">Belum ada destinasi yang difavoritkan.</p>
            @endif
        </div>

        <div class="bg-white border border-primary-100 rounded-2xl p-5 lg:col-span-2">
            <h3 class="font-semibold text-ink text-sm">Tren Pembuatan Itinerary</h3>
            <p class="text-xs text-ink/50">Jumlah itinerary baru per bulan, 6 bulan terakhir.</p>
            <div class="mt-4 h-64"><canvas id="chart-itinerary-trend"></canvas></div>
        </div>
    </div>

    <x-slot name="head">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const inkMuted = '#3A3A3A66';
                const gridColor = '#2F6B5A1A';

                const baseOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { color: gridColor }, ticks: { color: inkMuted, font: { size: 11 } } },
                        y: { grid: { display: false }, ticks: { color: inkMuted, font: { size: 11 } } },
                    },
                };

                @if ($categoryPopularity->sum('favorites_count') > 0)
                    new Chart(document.getElementById('chart-category-popularity'), {
                        type: 'bar',
                        data: {
                            labels: {{ Illuminate\Support\Js::from($categoryPopularity->pluck('name')) }},
                            datasets: [{
                                data: {{ Illuminate\Support\Js::from($categoryPopularity->pluck('favorites_count')) }},
                                backgroundColor: '#2F6B5A',
                                borderRadius: 4,
                                barThickness: 18,
                            }],
                        },
                        options: { ...baseOptions, indexAxis: 'y' },
                    });
                @endif

                @if ($topFavoritedDestinations->isNotEmpty())
                    new Chart(document.getElementById('chart-top-favorited'), {
                        type: 'bar',
                        data: {
                            labels: {{ Illuminate\Support\Js::from($topFavoritedDestinations->pluck('name')) }},
                            datasets: [{
                                data: {{ Illuminate\Support\Js::from($topFavoritedDestinations->pluck('favorites_count')) }},
                                backgroundColor: '#2F6B5A',
                                borderRadius: 4,
                                barThickness: 14,
                            }],
                        },
                        options: { ...baseOptions, indexAxis: 'y' },
                    });
                @endif

                new Chart(document.getElementById('chart-itinerary-trend'), {
                    type: 'bar',
                    data: {
                        labels: {{ Illuminate\Support\Js::from(collect($monthlyItineraryTrend)->pluck('label')) }},
                        datasets: [{
                            data: {{ Illuminate\Support\Js::from(collect($monthlyItineraryTrend)->pluck('total')) }},
                            backgroundColor: '#B56E80',
                            borderRadius: 4,
                            barThickness: 32,
                        }],
                    },
                    options: baseOptions,
                });
            });
        </script>
    </x-slot>
</x-admin-layout>
