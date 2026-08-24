<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">User</h2>
            <p class="text-sm text-gray-500">Daftar seluruh pengguna aplikasi</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button
                type="button"
                data-modal-open="createUserModal"
                class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-green-700"
            >
                <i class="fa-solid fa-plus"></i>
                Tambah Akun
            </button>

            <form action="{{ route('admin.import.datauser') }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2">
                @csrf
                <label for="userImportFile" class="inline-flex max-w-64 cursor-pointer items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                    <i class="fa-solid fa-file-excel text-green-600"></i>
                    <span id="userImportFileLabel" class="truncate">Pilih File</span>
                </label>
                <input id="userImportFile" type="file" name="file" accept=".xlsx,.xls" required class="hidden">
                <button id="userImportSubmit" type="submit" disabled class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-gray-300 disabled:text-gray-500 disabled:hover:bg-gray-300">
                    <i class="fa-solid fa-file-import"></i>
                    Import
                </button>
            </form>

            <a href="{{ route('admin.export.usertemplate') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-emerald-700">
                <i class="fa-solid fa-download"></i>
                Export Template
            </a>

            <button
                id="btnDeleteSelected"
                type="button"
                disabled
                class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:bg-gray-300 disabled:text-gray-500"
            >
                <i class="fa-solid fa-trash"></i>
                Hapus yang Dipilih
            </button>
        </div>
    </div>
</div>
