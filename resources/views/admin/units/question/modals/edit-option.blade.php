<div
    id="editOptionModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4"
>
    <div class="w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-xl">

        {{-- Header --}}
        <div class="flex items-start justify-between border-b border-gray-200 px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Edit Option
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Perbarui pilihan dan child jawaban.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="editOptionModal"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form
            id="editOptionForm"
            method="POST"
            action=""
        >
            @csrf
            @method('PUT')

            <input
                type="hidden"
                id="edit_option_id"
            >

            <input
                type="hidden"
                id="edit_option_question_id"
            >

            <div class="space-y-5 p-6">

                {{-- Nomor --}}
                <div>
                    <label
                        for="edit_option_no"
                        class="mb-2 block text-sm font-medium text-gray-700"
                    >
                        Nomor urut
                    </label>

                    <input
                        type="number"
                        id="edit_option_no"
                        name="no"
                        min="1"
                        required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm
                               focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    >
                </div>

                {{-- Option --}}
                <div>
                    <label
                        for="edit_option_answer_text"
                        class="mb-2 block text-sm font-medium text-gray-700"
                    >
                        Option
                    </label>

                    <textarea
                        id="edit_option_answer_text"
                        name="answer_text"
                        rows="3"
                        required
                        class="w-full resize-none rounded-lg border border-gray-300 px-4 py-2.5 text-sm
                               focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    ></textarea>
                </div>

                {{-- Child --}}
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">

                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-700">
                                Child jawaban
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                Aktifkan untuk menampilkan input jawaban tambahan.
                            </p>
                        </div>

                        <label class="relative inline-flex cursor-pointer items-center">
                            <input
                                type="checkbox"
                                id="edit_option_has_child_checkbox"
                                class="peer sr-only"
                            >

                            <div
                                class="h-6 w-11 rounded-full bg-gray-300 transition
                                       after:absolute after:left-[2px] after:top-[2px]
                                       after:h-5 after:w-5 after:rounded-full after:bg-white
                                       after:transition-all after:content-['']
                                       peer-checked:bg-indigo-600
                                       peer-checked:after:translate-x-full"
                            ></div>
                        </label>
                    </div>

                    <input
                        type="hidden"
                        id="edit_option_has_child"
                        name="has_child"
                        value="0"
                    >

                </div>

                {{-- Label Child --}}
                <div>
                    <label
                        for="edit_option_answer_text2"
                        class="mb-2 block text-sm font-medium text-gray-700"
                    >
                        Label child
                    </label>

                    <input
                        type="text"
                        id="edit_option_answer_text2"
                        name="answer_text2"
                        disabled
                        placeholder="Contoh: Jelaskan alasan Anda"
                        class="w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5
                               text-sm disabled:cursor-not-allowed"
                    >

                    <p class="mt-1.5 text-xs text-gray-400">
                        Label ini akan ditampilkan di bawah option.
                    </p>
                </div>

            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4">

                <button
                    type="button"
                    data-modal-close="editOptionModal"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium
                           text-gray-700 hover:bg-gray-50"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium
                           text-white hover:bg-indigo-700"
                >
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>
</div>