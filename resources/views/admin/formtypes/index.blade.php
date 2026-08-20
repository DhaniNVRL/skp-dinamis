@extends('admin.layouts.app-modern')

@section('title', 'Form Type')

@section('content')
<div class="mx-auto max-w-[1500px] space-y-6">
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-bold text-gray-900">Form Type</h1>
        <p class="mt-1 text-sm text-gray-500">Kelola tipe dan deskripsi form survei.</p>
    </div>

    @if ($errors->any())
        <div data-alert class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
            <p class="font-semibold">Data belum dapat diproses:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[420px_1fr]">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Tambah Form Type</h2>
            <form action="{{ route('formtype.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label for="form_type_name" class="mb-1 block text-sm font-medium text-gray-700">Nama</label>
                    <input id="form_type_name" name="name[]" value="{{ old('name.0') }}" required maxlength="255"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label for="form_type_description" class="mb-1 block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea id="form_type_description" name="description[]" required maxlength="1000" rows="4"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">{{ old('description.0') }}</textarea>
                </div>
                <button class="w-full rounded-lg bg-green-600 px-4 py-2 font-medium text-white hover:bg-green-700">
                    <i class="fa-solid fa-plus mr-1"></i> Tambah
                </button>
            </form>

            <div class="my-5 border-t border-gray-200"></div>

            <form action="{{ route('formtype.import') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <label for="form_type_file" class="block text-sm font-medium text-gray-700">Import Excel</label>
                <input id="form_type_file" type="file" name="file" accept=".xlsx,.xls" required
                    class="block w-full rounded-lg border border-gray-300 p-2 text-sm">
                <div class="flex gap-2">
                    <button class="flex-1 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">Import</button>
                    <a href="{{ route('formtype.export') }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Template</a>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <form action="{{ route('formtype.blukDelete') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex items-center justify-between border-b border-gray-200 p-4">
                    <p class="font-semibold text-gray-900">Daftar Form Type</p>
                    <button type="submit" class="rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700"
                        onclick="return confirm('Hapus form type terpilih yang tidak sedang digunakan?')">
                        Hapus yang Dipilih
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-left text-xs uppercase text-gray-600">
                            <tr>
                                <th class="px-4 py-3"><input type="checkbox" id="formTypeSelectAll" aria-label="Pilih semua"></th>
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Deskripsi</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($formTypes as $formType)
                                <tr>
                                    <td class="px-4 py-3"><input type="checkbox" name="selected[]" value="{{ $formType->id }}" class="form-type-checkbox"></td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $formType->id }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $formType->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $formType->description }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('formtype.edit', $formType->id) }}" class="text-amber-600 hover:text-amber-700" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500">Belum ada form type.</td></tr>
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
document.getElementById('formTypeSelectAll')?.addEventListener('change', function () {
    document.querySelectorAll('.form-type-checkbox').forEach((checkbox) => checkbox.checked = this.checked);
});
</script>
@endpush

