<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <h1 class="text-2xl font-bold text-ink">Kelola Kategori</h1>
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-600 transition">
                Tambah Kategori
            </a>
        </div>
    </x-slot>

    <div class="bg-white border border-primary-100 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-primary-50 text-primary-700">
                <tr>
                    <th class="text-left px-4 py-3 font-medium">Nama</th>
                    <th class="text-left px-4 py-3 font-medium">Slug</th>
                    <th class="text-left px-4 py-3 font-medium">Jumlah Destinasi</th>
                    <th class="text-right px-4 py-3 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary-50">
                @forelse ($categories as $category)
                    <tr>
                        <td class="px-4 py-3 font-medium text-ink">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-ink/60">{{ $category->slug }}</td>
                        <td class="px-4 py-3 text-ink/60">{{ $category->destinations_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-primary-700 hover:underline">Ubah</a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-3 text-red-500 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-ink/50">Belum ada kategori.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $categories->links() }}
    </div>
</x-admin-layout>
