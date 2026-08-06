<div
    class="rounded-lg border border-gray-200
        bg-white p-4 shadow-sm"
>
    <div
        class="flex flex-col gap-4
            lg:flex-row lg:items-center
            lg:justify-between"
    >
        {{-- TITLE --}}
        <div>
            <h2 class="text-xl font-semibold text-gray-800">
                Group
            </h2>

            <p class="text-sm text-gray-500">
                Daftar Group dari {{ $activity->name }}
            </p>
        </div>

        {{-- ACTION --}}
        <div class="flex flex-wrap items-center gap-2">
            {{-- CREATE --}}
            <button
                type="button"
                data-modal-open="createGroupModal"
                class="inline-flex items-center gap-2
                    rounded-lg bg-green-600
                    px-4 py-2 text-sm font-medium
                    text-white transition
                    hover:bg-green-700"
            >
                <i class="fa-solid fa-plus"></i>
                Tambah Group
            </button>

            {{-- IMPORT FORM --}}
            <form
                id="importGroupForm"
                action="{{ route('groups.import') }}"
                method="POST"
                enctype="multipart/form-data"
                class="flex flex-wrap items-center gap-2"
            >
                @csrf

                <input
                    type="hidden"
                    name="id_activities"
                    value="{{ $activity->id }}"
                >

                {{-- FILE LABEL --}}
                <label
                    for="groupImportFile"
                    class="inline-flex cursor-pointer
                        items-center gap-2 rounded-lg
                        border border-gray-300 bg-white
                        px-4 py-2 text-sm font-medium
                        text-gray-600 transition
                        hover:bg-gray-50"
                >
                    <i
                        class="fa-solid fa-file-excel
                            text-green-600"
                    ></i>

                    <span id="groupImportFileName">
                        Pilih File
                    </span>
                </label>

                {{-- FILE INPUT --}}
                <input
                    id="groupImportFile"
                    type="file"
                    name="file"
                    accept=".xlsx,.xls"
                    required
                    class="hidden"
                >

                {{-- IMPORT BUTTON --}}
                <button
                    id="btnImportGroup"
                    type="submit"
                    disabled
                    class="inline-flex items-center gap-2
                        rounded-lg bg-blue-600
                        px-4 py-2 text-sm font-medium
                        text-white transition
                        hover:bg-blue-700
                        disabled:cursor-not-allowed
                        disabled:bg-gray-300
                        disabled:text-gray-500"
                >
                    <i class="fa-solid fa-file-import"></i>
                    Import
                </button>
            </form>

            {{-- DOWNLOAD TEMPLATE --}}
            <a
                href="{{ route('groups.downloadTemplate') }}"
                class="inline-flex items-center gap-2
                    rounded-lg bg-emerald-600
                    px-4 py-2 text-sm font-medium
                    text-white transition
                    hover:bg-emerald-700"
            >
                <i class="fa-solid fa-download"></i>
                Download Template
            </a>

            {{-- BULK DELETE --}}
            <button
                id="btnDeleteSelected"
                type="button"
                disabled
                class="inline-flex items-center gap-2
                    rounded-lg bg-red-600
                    px-4 py-2 text-sm font-medium
                    text-white transition
                    hover:bg-red-700
                    disabled:cursor-not-allowed
                    disabled:bg-gray-300
                    disabled:text-gray-500"
            >
                <i class="fa-solid fa-trash"></i>
                Hapus yang Dipilih
            </button>
        </div>
    </div>
</div>