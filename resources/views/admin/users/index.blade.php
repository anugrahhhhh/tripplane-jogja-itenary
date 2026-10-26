<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-ink">Data Pengguna</h1>
        <p class="text-sm text-ink/60 mt-1">{{ $users->total() }} pengguna terdaftar.</p>
    </x-slot>

    <form method="GET" class="bg-white border border-primary-100 rounded-2xl p-4 flex gap-3 mb-6">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email..." class="flex-1 rounded-lg border-primary-100 text-sm focus:border-primary focus:ring-primary">
        <button type="submit" class="rounded-lg bg-primary-700 text-white text-sm font-semibold px-4 py-2 hover:bg-primary-800 transition">Cari</button>
    </form>

    <div class="bg-white border border-primary-100 rounded-2xl overflow-hidden overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-primary-50 text-primary-700">
                <tr>
                    <th class="text-left px-4 py-3 font-medium">Nama</th>
                    <th class="text-left px-4 py-3 font-medium">Email</th>
                    <th class="text-left px-4 py-3 font-medium">Role</th>
                    <th class="text-left px-4 py-3 font-medium">Favorit</th>
                    <th class="text-left px-4 py-3 font-medium">Ulasan</th>
                    <th class="text-left px-4 py-3 font-medium">Itinerary</th>
                    <th class="text-right px-4 py-3 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary-50">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-4 py-3 font-medium text-ink whitespace-nowrap">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-ink/60 whitespace-nowrap">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $user->role === 'admin' ? 'bg-accent-200 text-primary-800' : 'bg-primary-50 text-primary-700' }}">
                                {{ $user->role === 'admin' ? 'Admin' : 'Traveler' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-ink/60">{{ $user->favorites_count }}</td>
                        <td class="px-4 py-3 text-ink/60">{{ $user->reviews_count }}</td>
                        <td class="px-4 py-3 text-ink/60">{{ $user->itineraries_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-primary-700 hover:underline">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-ink/50">Tidak ada pengguna ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</x-admin-layout>
