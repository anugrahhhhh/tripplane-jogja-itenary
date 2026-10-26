<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-ink">Ubah Destinasi</h1>
    </x-slot>

    <form action="{{ route('admin.destinations.update', $destination) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-primary-100 rounded-2xl p-6 space-y-5">
        @csrf
        @method('PUT')
        @include('admin.destinations._form')

        <div class="flex justify-end gap-2 pt-2">
            <a href="{{ route('admin.destinations.index') }}" class="inline-flex items-center rounded-lg border border-primary-200 px-4 py-2.5 text-sm font-semibold text-primary-700 hover:bg-primary-50 transition">Batal</a>
            <x-primary-button>Simpan Perubahan</x-primary-button>
        </div>
    </form>
</x-admin-layout>
