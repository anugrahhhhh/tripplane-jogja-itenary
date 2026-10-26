@php
    $adminNav = [
        ['route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'label' => 'Dashboard'],
        ['route' => 'admin.categories.index', 'pattern' => 'admin.categories.*', 'label' => 'Kategori'],
        ['route' => 'admin.destinations.index', 'pattern' => 'admin.destinations.*', 'label' => 'Destinasi'],
        ['route' => 'admin.reviews.index', 'pattern' => 'admin.reviews.*', 'label' => 'Moderasi Ulasan'],
        ['route' => 'admin.users.index', 'pattern' => 'admin.users.*', 'label' => 'Data Pengguna'],
    ];
@endphp

<x-app-layout>
    @isset($header)
        <x-slot name="header">{{ $header }}</x-slot>
    @endisset

    @isset($head)
        <x-slot name="head">{{ $head }}</x-slot>
    @endisset

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <aside class="lg:col-span-1">
                <nav class="bg-white border border-primary-100 rounded-2xl p-2 space-y-1">
                    @foreach ($adminNav as $item)
                        <a href="{{ route($item['route']) }}"
                           class="block rounded-lg px-3 py-2 text-sm font-medium transition {{ request()->routeIs($item['pattern']) ? 'bg-primary-50 text-primary-700' : 'text-ink/70 hover:bg-primary-50' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
            </aside>

            <div class="lg:col-span-3">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-app-layout>
