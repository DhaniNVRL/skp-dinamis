<div
    id="deleteDescriptionModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-6"
>
    <div class="w-full max-w-md overflow-hidden rounded-xl bg-white shadow-xl">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">

            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Hapus Description
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Konfirmasi penghapusan description.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="deleteDescriptionModal"
                class="text-gray-400 transition hover:text-red-600"
            >
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>

        </div>

        <form
            id="deleteDescriptionForm"
            method="POST"
        >
            @csrf
            @method('DELETE')

            <input
                type="hidden"
                name="description_id"
                id="delete_description_id"
            >

            {{-- Body --}}
            <div class="p-6">

                <p class="text-sm leading-6 text-gray-600">
                    Description pada form
                    <span
                        id="delete_description_form_name"
                        class="font-semibold text-gray-800"
                    ></span>
                    akan dihapus secara permanen.
                </p>

                <p class="mt-3 text-sm font-medium text-red-600">
                    Data yang sudah dihapus tidak dapat dikembalikan.
                </p>

            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4">

                <button
                    type="button"
                    data-modal-close="deleteDescriptionModal"
                    class="rounded-lg border border-gray-300
                           px-4 py-2 text-gray-700
                           transition hover:bg-gray-100"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="rounded-lg bg-red-600 px-5 py-2
                           text-white transition hover:bg-red-700"
                >
                    <i class="fa-solid fa-trash mr-1"></i>
                    Hapus
                </button>

            </div>

        </form>

    </div>
</div>