<x-guest-layout>
    <div class="mb-4 text-sm text-ink/60">
        Terima kasih sudah mendaftar! Sebelum mulai, mohon verifikasi alamat emailmu dengan mengklik tautan yang baru saja kami kirim. Kalau belum menerima emailnya, kami akan kirim ulang.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-primary-700">
            Tautan verifikasi baru sudah dikirim ke alamat email yang kamu daftarkan.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>
                Kirim Ulang Email Verifikasi
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-ink/50 hover:text-primary">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
