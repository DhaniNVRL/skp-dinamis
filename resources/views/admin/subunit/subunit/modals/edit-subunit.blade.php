<div
    id="editSubUnitModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4"
>
    <div class="w-full max-w-lg overflow-hidden rounded-xl bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Edit Sub Unit
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Perbarui nama sub-unit.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="editSubUnitModal"
                class="text-gray-400 transition hover:text-red-600"
            >
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <form
            id="editSubUnitForm"
            method="POST"
        >
            @csrf
            @method('PUT')

            <div class="p-6">
                <label
                    for="editSubUnitName"
                    class="mb-2 block text-sm font-medium text-gray-700"
                >
                    Nama Sub Unit
                </label>

                <input
                    id="editSubUnitName"
                    type="text"
                    name="name"
                    maxlength="500"
                    required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                >
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4">
                <button
                    type="button"
                    data-modal-close="editSubUnitModal"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2 text-sm font-medium text-white hover:bg-amber-600"
                >
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>