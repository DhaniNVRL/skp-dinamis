<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Activity</h2>
            <p class="text-sm text-gray-500">Daftar seluruh Activity</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button
                type="button"
                data-modal-open="createActivityModal"
                class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-green-700"
            >
                <i class="fa-solid fa-plus"></i>
                Tambah Activity
            </button>

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
