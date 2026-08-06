<div
    id="createSubUnitModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4"
>
    <div class="flex max-h-[90vh] w-full max-w-3xl flex-col overflow-hidden rounded-xl bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Tambah Sub Unit
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Tambahkan satu atau beberapa sub-unit untuk
                    {{ $units->name }}.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="createSubUnitModal"
                class="text-gray-400 transition hover:text-red-600"
            >
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <form
            action="{{ route('subunits.store') }}"
            method="POST"
            class="flex min-h-0 flex-1 flex-col"
        >
            @csrf

            <input
                type="hidden"
                name="unit_id"
                value="{{ $units->id }}"
            >

            <div class="flex-1 overflow-y-auto p-6">
                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="w-16 px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600">
                                    No
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                                    Nama Sub Unit
                                </th>

                                <th class="w-20 px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600">
                                    Hapus
                                </th>
                            </tr>
                        </thead>

                        <tbody
                            id="createSubUnitBody"
                            class="divide-y divide-gray-200 bg-white"
                        ></tbody>
                    </table>
                </div>

                <button
                    id="addSubUnitRow"
                    type="button"
                    class="mt-4 inline-flex items-center gap-2 rounded-lg border border-blue-600 px-4 py-2 text-sm font-medium text-blue-600 transition hover:bg-blue-50"
                >
                    <i class="fa-solid fa-plus"></i>
                    Tambah Baris
                </button>
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4">
                <button
                    type="button"
                    data-modal-close="createSubUnitModal"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
                >
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>