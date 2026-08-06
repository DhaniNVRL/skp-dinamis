<div
    id="deleteFormModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4"
>
    <div class="w-full max-w-md overflow-hidden rounded-xl bg-white shadow-xl">

        {{-- Header --}}
        <div class="flex items-start justify-between border-b border-gray-200 px-6 py-4">

            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Hapus Form
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Konfirmasi penghapusan form.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="deleteFormModal"
                class="flex h-9 w-9 items-center justify-center
                       rounded-lg text-gray-500 transition hover:bg-gray-100"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>

        <form
            id="deleteFormForm"
            method="POST"
            action=""
        >
            @csrf
            @method('DELETE')

            <input
                type="hidden"
                id="delete_form_id"
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

                    <div class="min-w-0">

                        <h3 class="text-base font-semibold text-gray-800">
                            Hapus form ini?
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-gray-500">
                            Form
                            <span
                                id="delete_form_name"
                                class="font-semibold text-gray-700"
                            ></span>
                            akan dihapus secara permanen.
                        </p>

                        <div
                            id="delete_form_relation_warning"
                            class="mt-4 hidden rounded-lg border border-red-200
                                   bg-red-50 px-4 py-3"
                        >
                            <div class="flex items-start gap-2 text-sm text-red-700">

                                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>

                                <div>
                                    <p>
                                        Form ini memiliki
                                        <span
                                            id="delete_form_question_count"
                                            class="font-semibold"
                                        ></span>
                                        pertanyaan dan
                                        <span
                                            id="delete_form_option_count"
                                            class="font-semibold"
                                        ></span>
                                        option.
                                    </p>

                                    <p class="mt-1">
                                        Seluruh pertanyaan dan option tersebut
                                        juga akan dihapus.
                                    </p>
                                </div>

                            </div>
                        </div>

                        <p class="mt-3 text-sm font-medium text-red-600">
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>

                    </div>

                </div>

            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4">

                <button
                    type="button"
                    data-modal-close="deleteFormModal"
                    class="rounded-lg border border-gray-300 px-4 py-2
                           text-sm font-medium text-gray-700
                           transition hover:bg-gray-50"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-lg
                           bg-red-600 px-4 py-2 text-sm font-medium
                           text-white transition hover:bg-red-700"
                >
                    <i class="fa-solid fa-trash text-xs"></i>
                    Hapus Form
                </button>

            </div>

        </form>

    </div>
</div>