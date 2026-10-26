@props(['activeTab'])

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'JogjaTrip') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ink antialiased bg-cream" x-data>
        <div class="min-h-screen flex flex-col items-center justify-center px-4 py-10 gap-6">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-primary">
                Jogja<span class="text-accent-500">Trip</span>
            </a>

            <div class="w-full max-w-4xl bg-white rounded-3xl shadow-xl overflow-hidden grid grid-cols-1 lg:grid-cols-2">
                <div class="relative bg-primary-800 text-white p-8 sm:p-10 flex flex-col justify-between overflow-hidden order-2 lg:order-1 min-h-[280px]">
                    <div class="absolute -top-12 -right-12 w-56 h-56 rounded-full bg-white/10 pointer-events-none"></div>
                    <div class="absolute -bottom-16 -left-10 w-40 h-40 rounded-full bg-white/5 pointer-events-none"></div>
                    <div class="relative">{{ $aside }}</div>
                </div>

                <div class="p-8 sm:p-10 order-1 lg:order-2">
                    <div class="inline-flex rounded-full bg-cream p-1 text-sm font-semibold">
                        <a href="{{ route('login') }}" class="rounded-full px-5 py-2 transition {{ $activeTab === 'login' ? 'bg-white shadow-sm text-ink' : 'text-ink/40 hover:text-ink' }}">Masuk</a>
                        <a href="{{ route('register') }}" class="rounded-full px-5 py-2 transition {{ $activeTab === 'register' ? 'bg-white shadow-sm text-ink' : 'text-ink/40 hover:text-ink' }}">Daftar</a>
                    </div>

                    <div class="mt-6">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
