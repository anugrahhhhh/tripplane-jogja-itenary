<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <h1 class="text-2xl font-bold text-ink">Kelola Destinasi</h1>
            <a href="{{ route('admin.destinations.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-600 transition">
                Tambah Destinasi
            </a>
        </div>
    </x-slot>

    <form method="GET" class="bg-white border border-primary-100 rounded-2xl p-4 flex flex-wrap gap-3 mb-6">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama destinasi..." class="flex-1 min-w-[200px] rounded-lg border-primary-100 text-sm focus:border-primary focus:ring-primary">
        <select name="kategori" class="rounded-lg border-primary-100 text-sm focus:border-primary focus:ring-primary">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) request('kategori') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg bg-primary-700 text-white text-sm font-semibold px-4 py-2 hover:bg-primary-800 transition">Cari</button>
    </form>

    <div class="bg-white border border-primary-100 rounded-2xl overflow-hidden overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-primary-50 text-primary-700">
                <tr>
                    <th class="text-left px-4 py-3 font-medium">Destinasi</th>
                    <th class="text-left px-4 py-3 font-medium">Kategori</th>
                    <th class="text-left px-4 py-3 font-medium">Harga</th>
                    <th class="text-left px-4 py-3 font-medium">Status</th>
                    <th class="text-right px-4 py-3 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary-50">
                @forelse ($destinations as $destination)
                    <tr>
                        <td class="px-4 py-3 font-medium text-ink whitespace-nowrap">{{ $destination->name }}</td>
                        <td class="px-4 py-3 text-ink/60 whitespace-nowrap">{{ $destination->category->name }}</td>
                        <td class="px-4 py-3 text-ink/60 whitespace-nowrap">
                            @if ($destination->price_min == 0 && $destination->price_max == 0)
                                Gratis
                            @else
                                Rp{{ number_format($destination->price_min) }} - Rp{{ number_format($destination->price_max) }}
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $destination->is_active ? 'bg-primary-50 text-primary-700' : 'bg-red-50 text-red-600' }}">
                                {{ $destination->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('destinations.show', $destination) }}" target="_blank" class="text-ink/50 hover:underline">Lihat</a>
                            <a href="{{ route('admin.destinations.edit', $destination) }}" class="ml-3 text-primary-700 hover:underline">Ubah</a>
                            <form action="{{ route('admin.destinations.destroy', $destination) }}" method="POST" class="inline" onsubmit="return confirm('Hapus destinasi ini beserta seluruh foto, favorit, ulasan, dan riwayatnya?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-3 text-red-500 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-ink/50">Tidak ada destinasi ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $destinations->links() }}
    </div>
</x-admin-layout>
