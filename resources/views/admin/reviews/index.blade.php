<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-ink">Moderasi Ulasan</h1>
        <p class="text-sm text-ink/60 mt-1">{{ $reviews->total() }} ulasan total.</p>
    </x-slot>

    <div class="mb-4 flex gap-2">
        <a href="{{ route('admin.reviews.index') }}" class="rounded-lg px-3 py-1.5 text-xs font-medium {{ ! request('status') ? 'bg-primary text-white' : 'bg-white border border-primary-100 text-primary-700' }}">Semua</a>
        <a href="{{ route('admin.reviews.index', ['status' => 'tampil']) }}" class="rounded-lg px-3 py-1.5 text-xs font-medium {{ request('status') === 'tampil' ? 'bg-primary text-white' : 'bg-white border border-primary-100 text-primary-700' }}">Tampil</a>
        <a href="{{ route('admin.reviews.index', ['status' => 'sembunyi']) }}" class="rounded-lg px-3 py-1.5 text-xs font-medium {{ request('status') === 'sembunyi' ? 'bg-primary text-white' : 'bg-white border border-primary-100 text-primary-700' }}">Disembunyikan</a>
    </div>

    <div class="space-y-3">
        @forelse ($reviews as $review)
            <div class="bg-white border border-primary-100 rounded-xl p-4">
                <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div>
                        <p class="font-medium text-ink text-sm">{{ $review->user->name }} &rarr; {{ $review->destination->name }}</p>
                        <div class="flex items-center gap-0.5 mt-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-amber-400' : 'text-ink/15' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.447a1 1 0 00-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.538 1.118l-3.367-2.447a1 1 0 00-1.176 0l-3.367 2.447c-.783.57-1.838-.196-1.538-1.118l1.287-3.957a1 1 0 00-.363-1.118L2.062 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.957z" /></svg>
                            @endfor
                        </div>
                        @if ($review->comment)
                            <p class="mt-2 text-sm text-ink/70">{{ $review->comment }}</p>
                        @endif
                        <p class="mt-1 text-xs text-ink/40">{{ $review->created_at->translatedFormat('d F Y H:i') }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $review->is_visible ? 'bg-primary-50 text-primary-700' : 'bg-red-50 text-red-600' }}">
                            {{ $review->is_visible ? 'Tampil' : 'Disembunyikan' }}
                        </span>
                        <form action="{{ route('admin.reviews.update', $review) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs font-semibold text-primary-700 hover:underline">
                                {{ $review->is_visible ? 'Sembunyikan' : 'Tampilkan' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Hapus ulasan ini secara permanen?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-semibold text-red-500 hover:underline">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16 bg-white rounded-2xl border border-primary-100">
                <p class="text-ink/50">Tidak ada ulasan pada filter ini.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $reviews->links() }}
    </div>
</x-admin-layout>
