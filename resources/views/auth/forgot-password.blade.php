<x-guest-layout>
    <div class="mb-4 text-sm text-ink/60">
        Lupa kata sandi? Tidak masalah. Masukkan alamat email-mu dan kami akan kirimkan tautan untuk membuat kata sandi baru.
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button class="w-full justify-center">
                Kirim Tautan Reset Kata Sandi
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
