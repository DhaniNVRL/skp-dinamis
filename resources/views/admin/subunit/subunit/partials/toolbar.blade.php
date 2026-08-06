<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        {{-- TITLE --}}
        <div>
            <h2 class="text-xl font-semibold text-gray-800">
                Sub Unit
            </h2>

            <p class="text-sm text-gray-500">
                Daftar Sub Unit dari {{ $units->name }}
            </p>
        </div>

        {{-- ACTION --}}
        <div class="flex flex-wrap items-center gap-2">
            {{-- CREATE --}}
            <button
                type="button"
                data-modal-open="createSubUnitModal"
                class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-green-700"
            >
                <i class="fa-solid fa-plus"></i>
                Tambah Sub Unit
            </button>

            {{-- IMPORT --}}
            <form
                id="importSubUnitForm"
                action="{{ route('subunits.import') }}"
                method="POST"
                enctype="multipart/form-data"
                class="flex flex-wrap items-center gap-2"
            >
                @csrf

                <input
                    type="hidden"
                    name="unit_id"
                    value="{{ $units->id }}"
                >

                <label
                    for="subUnitImportFile"
                    class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
                >
                    <i class="fa-solid fa-file-excel text-green-600"></i>

                    <span id="subUnitImportFileName">
                        Pilih File
                    </span>
                </label>

                <input
                    id="subUnitImportFile"
                    type="file"
                    name="file"
                    accept=".xlsx,.xls,.csv"
                    required
                    class="hidden"
                >

                <button
                    id="btnImportSubUnit"
                    type="submit"
                    disabled
                    class="inline-flex items-center gap-2 rounded-lg
                        bg-blue-600 px-4 py-2 text-sm font-medium
                        text-white transition hover:bg-blue-700
                        disabled:cursor-not-allowed
                        disabled:bg-gray-300
                        disabled:text-gray-500"
                >
                    <i class="fa-solid fa-file-import"></i>
                    Import
                </button>
            </form>

            {{-- EXPORT TEMPLATE --}}
            <a
                href="{{ route('subunits.export', [
                    'unitId' => $units->id,
                ]) }}"
                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-emerald-700"
            >
                <i class="fa-solid fa-download"></i>
                Export Template
            </a>

            {{-- BULK DELETE --}}
            <button
                id="btnDeleteSelectedSubUnit"
                type="button"
                disabled
                class="inline-flex items-center gap-2 rounded-lg
                    bg-red-600 px-4 py-2 text-sm font-medium
                    text-white transition hover:bg-red-700
                    disabled:cursor-not-allowed disabled:bg-gray-300
                    disabled:text-gray-500"
            >
                <i class="fa-solid fa-trash"></i>
                Hapus yang Dipilih
            </button>
        </div>
    </div>
</div>