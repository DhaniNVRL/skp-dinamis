<div
    id="bulkDeleteUnitModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4"
>
    <div class="w-full max-w-md overflow-hidden rounded-xl bg-white shadow-xl">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Hapus Unit Terpilih
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Konfirmasi penghapusan data unit.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="bulkDeleteUnitModal"
                class="text-gray-400 transition hover:text-red-600"
            >
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        {{-- Form --}}
        <form
            id="bulkDeleteUnitForm"
            method="POST"
            action="{{ route('units.bulkDelete') }}"
        >
            @csrf
            @method('DELETE')

            <div id="bulkUnitIds"></div>

            <div class="px-6 py-5">
                <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-triangle-exclamation mt-1 text-red-600"></i>

                        <div>
                            <p class="font-medium text-red-800">
                                Apakah Anda yakin?
                            </p>

                            <p class="mt-1 text-sm text-red-700">
                                Sebanyak
                                <span
                                    id="bulkDeleteUnitCount"
                                    class="font-bold"
                                >
                                    0
                                </span>
                                unit akan dihapus.
                            </p>

                            <p class="mt-2 text-sm text-red-700">
                                Data yang sudah dihapus tidak dapat dikembalikan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4">
                <button
                    type="button"
                    data-modal-close="bulkDeleteUnitModal"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2
                           text-sm font-medium text-gray-700 transition
                           hover:bg-gray-100"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-lg
                           bg-red-600 px-4 py-2 text-sm font-medium
                           text-white transition hover:bg-red-700"
                >
                    <i class="fa-solid fa-trash"></i>
                    Hapus
                </button>
            </div>
        </form>
    </div>
</div>