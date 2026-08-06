<div
    id="createQuestionModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4"
>
    <div class="flex max-h-[90vh] w-full max-w-6xl flex-col overflow-hidden rounded-xl bg-white shadow-xl">

        {{-- Header --}}
        <div class="flex items-start justify-between border-b border-gray-200 px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Tambah Pertanyaan
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Tambahkan satu atau beberapa pertanyaan.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="createQuestionModal"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form
            id="createQuestionForm"
            method="POST"
            class="flex min-h-0 flex-1 flex-col"
        >
            @csrf

            <input
                type="hidden"
                name="group_id"
                id="create_question_group_id"
            >

            <input
                type="hidden"
                name="form_id"
                id="create_question_form_id"
            >

            <input
                type="hidden"
                name="formtype_id"
                id="create_question_formtype_id"
            >

            {{-- Body --}}
            <div class="min-h-0 flex-1 overflow-auto p-6">
                {{-- Isi modal --}}
            </div>

            {{-- Body --}}
            <div class="min-h-0 flex-1 overflow-auto p-6">

                <div class="overflow-x-auto rounded-lg border border-gray-200">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-100">
                            <tr>
                                <th class="w-24 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                                    Header
                                </th>

                                <th class="w-24 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                                    No
                                </th>

                                <th class="min-w-72 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                                    Pertanyaan
                                </th>

                                <th class="min-w-52 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                                    Tipe
                                </th>

                                <th class="w-20 px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody
                            id="createQuestionBody"
                            class="divide-y divide-gray-200 bg-white"
                        ></tbody>

                    </table>

                </div>

                <button
                    type="button"
                    id="addQuestionRow"
                    class="mt-4 inline-flex items-center gap-2 rounded-lg border border-indigo-600 px-4 py-2 text-sm font-medium text-indigo-600 hover:bg-indigo-50"
                >
                    <i class="fa-solid fa-plus"></i>
                    Tambah Baris
                </button>

            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4">

                <button
                    type="button"
                    data-modal-close="createQuestionModal"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    Simpan Pertanyaan
                </button>

            </div>

        </form>

    </div>
</div>