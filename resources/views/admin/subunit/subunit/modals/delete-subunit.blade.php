<div
    id="deleteSubUnitModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4"
>
    <div class="w-full max-w-md overflow-hidden rounded-xl bg-white shadow-xl">
        {{-- HEADER --}}
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Hapus Sub Unit
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Konfirmasi penghapusan Sub Unit.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="deleteSubUnitModal"
                class="text-gray-400 transition hover:text-red-600"
            >
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        {{-- FORM --}}
        <form
            id="deleteSubUnitForm"
            method="POST"
        >
            @csrf
            @method('DELETE')

            <div class="space-y-4 p-6">
                <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                    <p class="text-sm text-red-700">
                        Anda akan menghapus Sub Unit:
                    </p>

                    <p
                        id="deleteSubUnitName"
                        class="mt-2 font-semibold text-red-800"
                    >
                        -
                    </p>
                </div>

                <p class="text-sm text-gray-500">
                    Data yang sudah dihapus tidak dapat dikembalikan.
                    Mapping pertanyaan yang terhubung dengan Sub Unit ini
                    juga akan dihapus.
                </p>
            </div>

            {{-- FOOTER --}}
            <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4">
                <button
                    type="button"
                    data-modal-close="deleteSubUnitModal"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-100"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-700"
                >
                    <i class="fa-solid fa-trash"></i>
                    Hapus
                </button>
            </div>
        </form>
    </div>
</div>