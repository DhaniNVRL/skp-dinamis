<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

    {{-- Informasi Halaman --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">
            Unit
        </h1>
    </div>
    {{-- Tombol Aksi --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('users.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
            <i class="fa-solid fa-user-plus"></i>
            Tambah Unit
        </a>
    </div>
</div>