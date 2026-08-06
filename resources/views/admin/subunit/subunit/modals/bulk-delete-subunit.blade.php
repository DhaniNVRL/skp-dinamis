<div
    id="bulkDeleteSubUnitModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4"
>
    <div class="w-full max-w-md overflow-hidden rounded-xl bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Hapus Sub Unit Terpilih
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Konfirmasi penghapusan beberapa Sub Unit.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="bulkDeleteSubUnitModal"
                class="text-gray-400 transition hover:text-red-600"
            >
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <form
            id="bulkDeleteSubUnitForm"
            action="{{ route('subunits.bulk-delete') }}"
            method="POST"
        >
            @csrf
            @method('DELETE')

            <input
                type="hidden"
                name="unit_id"
                value="{{ $units->id }}"
            >

            <div id="bulkSubUnitIds"></div>

            <div class="p-6">
                <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                    <p class="text-sm text-red-700">
                        Anda akan menghapus

                        <span
                            id="bulkDeleteCount"
                            class="font-bold"
                        >
                            0
                        </span>

                        Sub Unit terpilih.
                    </p>
                </div>

                <p class="mt-4 text-sm text-gray-500">
                    Mapping pertanyaan yang terhubung dengan Sub Unit tersebut juga akan dihapus.
                </p>
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4">
                <button
                    type="button"
                    data-modal-close="bulkDeleteSubUnitModal"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                >
                    <i class="fa-solid fa-trash"></i>
                    Hapus Semua
                </button>
            </div>
        </form>
    </div>
</div>