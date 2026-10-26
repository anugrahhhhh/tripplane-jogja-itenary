@php $category = $category ?? null; @endphp

<div>
    <x-input-label for="name" value="Nama Kategori" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $category?->name) }}" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-1" />
</div>

<div>
    <x-input-label for="icon" value="Kode Ikon (opsional)" />
    <x-text-input id="icon" name="icon" type="text" class="mt-1 block w-full" value="{{ old('icon', $category?->icon) }}" placeholder="Contoh: kuliner, budaya-sejarah" />
    <p class="mt-1 text-xs text-ink/50">Dipakai untuk menampilkan ikon kategori yang sesuai di tampilan katalog.</p>
    <x-input-error :messages="$errors->get('icon')" class="mt-1" />
</div>
