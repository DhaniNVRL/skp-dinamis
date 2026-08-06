<div
    id="deleteOptionModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4"
>
    <div class="w-full max-w-md overflow-hidden rounded-xl bg-white shadow-xl">

        {{-- Header --}}
        <div class="flex items-start justify-between border-b border-gray-200 px-6 py-4">

            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Hapus Option
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Konfirmasi penghapusan pilihan jawaban.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="deleteOptionModal"
                class="flex h-9 w-9 items-center justify-center rounded-lg
                       text-gray-500 hover:bg-gray-100"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>

        <form
            id="deleteOptionForm"
            method="POST"
            action=""
        >
            @csrf
            @method('DELETE')

            <input
                type="hidden"
                id="delete_option_id"
            >

            {{-- Body --}}
            <div class="p-6">

                <div class="flex items-start gap-4">

                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center
                               rounded-full bg-red-100 text-red-600"
                    >
                        <i class="fa-solid fa-trash"></i>
                    </div>

                    <div>
                        <h3 class="text-base font-semibold text-gray-800">
                            Hapus pilihan ini?
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-gray-500">
                            Option
                            <span
                                id="delete_option_name"
                                class="font-semibold text-gray-700"
                            ></span>
                            akan dihapus secara permanen.
                        </p>

                        <p class="mt-2 text-sm text-red-600">
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>
                    </div>

                </div>

            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4">

                <button
                    type="button"
                    data-modal-close="deleteOptionModal"
                    class="rounded-lg border border-gray-300 px-4 py-2
                           text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-lg
                           bg-red-600 px-4 py-2 text-sm font-medium
                           text-white hover:bg-red-700"
                >
                    <i class="fa-solid fa-trash text-xs"></i>
                    Hapus Option
                </button>

            </div>

        </form>

    </div>
</div>