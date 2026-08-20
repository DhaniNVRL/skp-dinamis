@extends('admin.layouts.app-modern')

@section('title', 'Roles')

@section('content')
<div class="mx-auto max-w-[1500px] space-y-6">
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-bold text-gray-900">Roles</h1>
        <p class="mt-1 text-sm text-gray-500">Kelola ID, nama, dan hak akses role aplikasi.</p>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
            <p class="font-semibold">Data belum dapat diproses:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif
    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
    @endif
    @if (session('successdelete'))
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">{{ session('successdelete') }}</div>
    @endif
    @if (session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">{{ session('error') }}</div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[420px_1fr]">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Tambah Role</h2>
            <p class="mt-1 text-sm text-gray-500">ID role diisi manual dan harus unik.</p>
            <form action="{{ route('roles.storeroles') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label for="role_id" class="mb-1 block text-sm font-medium text-gray-700">ID Role</label>
                    <input id="role_id" type="number" name="id[]" value="{{ old('id.0') }}" min="1" step="1" required placeholder="Contoh: 5"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label for="role_name" class="mb-1 block text-sm font-medium text-gray-700">Nama Role</label>
                    <input id="role_name" name="name[]" value="{{ old('name.0') }}" required maxlength="191" placeholder="Masukkan nama role"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                </div>
                <button class="w-full rounded-lg bg-green-600 px-4 py-2 font-medium text-white hover:bg-green-700">
                    <i class="fa-solid fa-plus mr-1"></i> Tambah
                </button>
            </form>

            <div class="my-5 border-t border-gray-200"></div>

            <form action="{{ route('roles.import') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <label for="role_file" class="block text-sm font-medium text-gray-700">Import Excel</label>
                <input id="role_file" type="file" name="file" accept=".xlsx,.xls" required
                    class="block w-full rounded-lg border border-gray-300 p-2 text-sm">
                <div class="flex gap-2">
                    <button class="flex-1 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">Import</button>
                    <a href="{{ route('roles.export') }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Template</a>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <form id="roleBulkDeleteForm" action="{{ route('roles.bulkDelete') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 p-4">
                    <p class="font-semibold text-gray-900">Daftar Roles</p>
                    <button type="submit" class="rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700">Hapus yang Dipilih</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-left text-xs uppercase text-gray-600">
                            <tr>
                                <th class="px-4 py-3"><input type="checkbox" id="roleSelectAll" aria-label="Pilih semua role"></th>
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Diperbarui</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($roles as $role)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3"><input type="checkbox" name="selected[]" value="{{ $role->id }}" class="role-checkbox"></td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $role->id }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $role->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ optional($role->updated_at)->format('d-m-Y H:i') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('roles.edit', $role->id) }}" class="text-amber-600 hover:text-amber-700" title="Edit role"><i class="fa-solid fa-pen"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500">Belum ada role.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('roleSelectAll')?.addEventListener('change', function () {
    document.querySelectorAll('.role-checkbox').forEach((checkbox) => checkbox.checked = this.checked);
});
document.getElementById('roleBulkDeleteForm')?.addEventListener('submit', function (event) {
    const selected = document.querySelectorAll('.role-checkbox:checked').length;
    if (selected === 0 || !confirm('Hapus role terpilih yang tidak sedang digunakan?')) event.preventDefault();
});
</script>
@endpush
