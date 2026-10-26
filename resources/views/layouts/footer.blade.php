<footer class="bg-primary-900 text-primary-50 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
            <div>
                <p class="text-xl font-bold text-white">Jogja<span class="text-accent-300">Trip</span></p>
                <p class="mt-2 text-sm text-primary-200">Rencanakan liburanmu ke Yogyakarta — dari rekomendasi tempat sampai jadwal itinerary, semua dalam satu aplikasi.</p>
            </div>
            <div>
                <p class="font-semibold text-white">Jelajahi</p>
                <ul class="mt-2 space-y-1 text-sm text-primary-200">
                    <li><a href="{{ route('destinations.index') }}" class="hover:text-white">Katalog Destinasi</a></li>
                    <li><a href="{{ route('nearby.index') }}" class="hover:text-white">Rekomendasi Terdekat</a></li>
                </ul>
            </div>
            <div>
                <p class="font-semibold text-white">Akun</p>
                <ul class="mt-2 space-y-1 text-sm text-primary-200">
                    @auth
                        <li><a href="{{ route('itineraries.index') }}" class="hover:text-white">Itinerary Saya</a></li>
                        <li><a href="{{ route('favorites.index') }}" class="hover:text-white">Favorit</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="hover:text-white">Profil</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="hover:text-white">Masuk</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white">Daftar</a></li>
                    @endauth
                </ul>
            </div>
        </div>
        <p class="mt-8 text-xs text-primary-300">&copy; {{ date('Y') }} JogjaTrip. Dibuat untuk membantu rencana wisata Yogyakarta.</p>
    </div>
</footer>
