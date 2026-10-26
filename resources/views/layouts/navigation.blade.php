<nav x-data="{ open: false }" class="bg-white border-b border-primary-100 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-primary">
                        Jogja<span class="text-accent-500">Trip</span>
                    </a>
                </div>

                <div class="hidden space-x-1 sm:-my-px sm:ms-10 sm:flex sm:items-center">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">Beranda</x-nav-link>
                    <x-nav-link :href="route('destinations.index')" :active="request()->routeIs('destinations.*')">Katalog</x-nav-link>
                    <x-nav-link :href="route('nearby.index')" :active="request()->routeIs('nearby.*')">Terdekat</x-nav-link>
                    <x-nav-link :href="route('recommendations.create')" :active="request()->routeIs('recommendations.*')">Rekomendasi AI</x-nav-link>
                    @auth
                        <x-nav-link :href="route('favorites.index')" :active="request()->routeIs('favorites.*')">Favorit</x-nav-link>
                        <x-nav-link :href="route('itineraries.index')" :active="request()->routeIs('itineraries.*')">Itinerary</x-nav-link>
                        @if (auth()->user()->isAdmin())
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">Panel Admin</x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                @auth
                    @php $unreadNotifications = auth()->user()->unreadNotifications; @endphp
                    <x-dropdown align="right" width="w-80">
                        <x-slot name="trigger">
                            <button class="relative w-9 h-9 rounded-full border border-primary-100 hover:border-primary-300 transition flex items-center justify-center text-ink/60">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                                @if ($unreadNotifications->isNotEmpty())
                                    <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-accent-500 text-white text-[10px] font-bold flex items-center justify-center">
                                        {{ $unreadNotifications->count() > 9 ? '9+' : $unreadNotifications->count() }}
                                    </span>
                                @endif
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="px-4 py-2 border-b border-primary-50 flex items-center justify-between">
                                <p class="text-sm font-semibold text-ink">Notifikasi</p>
                                @if ($unreadNotifications->isNotEmpty())
                                    <form action="{{ route('notifications.read-all') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs font-medium text-accent-500 hover:underline">Tandai semua dibaca</button>
                                    </form>
                                @endif
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                @forelse (auth()->user()->notifications()->latest()->limit(8)->get() as $notification)
                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2.5 hover:bg-primary-50 transition flex items-start gap-2">
                                            @unless ($notification->read_at)
                                                <span class="w-1.5 h-1.5 rounded-full bg-accent-500 mt-1.5 shrink-0"></span>
                                            @endunless
                                            <div class="min-w-0">
                                                <p class="text-xs text-ink {{ $notification->read_at ? 'text-ink/60' : 'font-medium' }} line-clamp-2">{{ $notification->data['message'] ?? 'Notifikasi' }}</p>
                                                <p class="mt-0.5 text-[11px] text-ink/40">{{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </button>
                                    </form>
                                @empty
                                    <p class="px-4 py-6 text-sm text-ink/40 text-center">Belum ada notifikasi.</p>
                                @endforelse
                            </div>
                            <div class="border-t border-primary-50">
                                <x-dropdown-link :href="route('notifications.index')">Lihat semua notifikasi</x-dropdown-link>
                            </div>
                        </x-slot>
                    </x-dropdown>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 rounded-full pl-1 pr-3 py-1 border border-primary-100 hover:border-primary-300 transition">
                                <span class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-sm font-semibold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                                <span class="text-sm text-ink">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 text-ink/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="px-4 py-2 border-b border-primary-50">
                                <p class="text-xs text-ink/50">Masuk sebagai</p>
                                <p class="text-sm font-medium text-ink truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <x-dropdown-link :href="route('profile.edit')">Profil Saya</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    Keluar
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-ink/70 hover:text-primary">Masuk</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-600 transition">Daftar</a>
                @endauth
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 text-ink/60 hover:bg-primary-50 hover:text-primary focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-primary-100 bg-white">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">Beranda</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('destinations.index')" :active="request()->routeIs('destinations.*')">Katalog</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('nearby.index')" :active="request()->routeIs('nearby.*')">Terdekat</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('recommendations.create')" :active="request()->routeIs('recommendations.*')">Rekomendasi AI</x-responsive-nav-link>
            @auth
                <x-responsive-nav-link :href="route('favorites.index')" :active="request()->routeIs('favorites.*')">Favorit</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('itineraries.index')" :active="request()->routeIs('itineraries.*')">Itinerary</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')">
                    Notifikasi
                    @if (auth()->user()->unreadNotifications->isNotEmpty())
                        <span class="ml-1 inline-flex items-center rounded-full bg-accent-500 text-white text-[10px] font-bold px-1.5 py-0.5">{{ auth()->user()->unreadNotifications->count() }}</span>
                    @endif
                </x-responsive-nav-link>
                @if (auth()->user()->isAdmin())
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">Panel Admin</x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <div class="pt-4 pb-3 border-t border-primary-100">
            @auth
                <div class="px-4 flex items-center gap-3">
                    <span class="w-9 h-9 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-sm font-semibold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <div>
                        <div class="font-medium text-base text-ink">{{ auth()->user()->name }}</div>
                        <div class="text-sm text-ink/50">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">Profil Saya</x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            Keluar
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="space-y-1 px-4">
                    <a href="{{ route('login') }}" class="block py-2 text-base font-medium text-ink/70">Masuk</a>
                    <a href="{{ route('register') }}" class="block py-2 text-base font-semibold text-primary">Daftar</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
