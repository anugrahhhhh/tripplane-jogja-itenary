<x-auth-split-layout activeTab="register">
    <x-slot name="aside">
        <p class="text-sm font-medium text-primary-200">Trip Planner</p>
        <h1 class="mt-6 text-3xl font-bold text-white text-balance">Mulai susun perjalanan impianmu.</h1>
        <p class="mt-3 text-primary-100 text-sm max-w-xs">Gabungkan destinasi favoritmu dalam satu tempat. Simpan tempat yang kamu suka dan susun perjalanan berdasarkan hari dengan lebih teratur.</p>

        <div class="mt-10 grid grid-cols-3 gap-2">
            <div class="rounded-xl bg-white/10 p-3">
                <p class="text-xs font-semibold text-white">♡ Favorite</p>
                <p class="mt-1 text-[10.5px] leading-tight text-primary-200">Simpan tempat favoritmu</p>
            </div>
            <div class="rounded-xl bg-white/10 p-3">
                <p class="text-xs font-semibold text-white">☰ Itinerary</p>
                <p class="mt-1 text-[10.5px] leading-tight text-primary-200">Atur perjalananmu</p>
            </div>
            <div class="rounded-xl bg-white/10 p-3">
                <p class="text-xs font-semibold text-white">⌕ Explore</p>
                <p class="mt-1 text-[10.5px] leading-tight text-primary-200">Temukan tempat baru</p>
            </div>
        </div>
    </x-slot>

    <h2 class="text-2xl font-bold text-ink">Buat akun baru</h2>
    <p class="mt-1 text-sm text-ink/60">Daftar untuk mulai menyimpan destinasi dan membuat itinerary perjalananmu.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-xs font-semibold uppercase tracking-wide text-ink/50">Nama Lengkap</label>
            <x-text-input id="name" class="block mt-1.5 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Masukkan nama lengkap" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wide text-ink/50">Email</label>
            <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold uppercase tracking-wide text-ink/50">Password</label>
            <x-text-input id="password" class="block mt-1.5 w-full" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
            <p class="mt-1 text-xs text-ink/40">Gunakan minimal 8 karakter agar akunmu lebih aman.</p>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wide text-ink/50">Konfirmasi Password</label>
            <x-text-input id="password_confirmation" class="block mt-1.5 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Masukkan kembali password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <label class="flex items-start gap-2 text-sm text-ink/60">
            <input type="checkbox" required class="mt-0.5 rounded border-ink/20 text-primary focus:ring-primary">
            <span>Saya menyetujui <strong class="text-ink">syarat &amp; ketentuan</strong> JogjaTrip.</span>
        </label>

        <button type="submit" class="w-full rounded-xl bg-primary py-3 text-sm font-semibold text-white hover:bg-primary-600 transition">
            Buat Akun
        </button>
    </form>

    <div class="mt-6 flex items-center gap-3">
        <div class="flex-1 h-px bg-primary-100"></div>
        <span class="text-xs text-ink/40">atau</span>
        <div class="flex-1 h-px bg-primary-100"></div>
    </div>

    <div class="mt-4">
        <x-google-button />
    </div>

    <p class="mt-6 text-center text-sm text-ink/60">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-semibold text-accent-500 hover:underline">Masuk sekarang</a>
    </p>
</x-auth-split-layout>
