<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-ink">Ubah Kategori</h1>
    </x-slot>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="bg-white border border-primary-100 rounded-2xl p-6 space-y-4">
        @csrf
        @method('PUT')
        @include('admin.categories._form')

        <div class="flex justify-end gap-2 pt-2">
            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center rounded-lg border border-primary-200 px-4 py-2.5 text-sm font-semibold text-primary-700 hover:bg-primary-50 transition">Batal</a>
            <x-primary-button>Simpan Perubahan</x-primary-button>
        </div>
    </form>
</x-admin-layout>
