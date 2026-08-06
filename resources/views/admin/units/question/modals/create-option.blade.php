<div
    id="createOptionModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center
           bg-black/50 p-4"
>
    <div
        class="flex max-h-[90vh] w-full max-w-6xl
               flex-col overflow-hidden rounded-xl bg-white shadow-xl"
    >

        {{-- Header --}}
        <div
            class="flex items-start justify-between
                   border-b border-gray-200 px-6 py-4"
        >
            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Tambah Option
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Tambahkan pilihan jawaban untuk pertanyaan:
                    <span
                        id="create_option_question_name"
                        class="font-medium text-gray-700"
                    ></span>
                </p>
            </div>

            <button
                type="button"
                data-modal-close="createOptionModal"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>


        <form
            id="createOptionForm"
            method="POST"
            class="flex min-h-0 flex-1 flex-col"
        >
            @csrf

            <input
                type="hidden"
                name="question_id"
                id="create_option_question_id"
            >


            {{-- Body --}}
            <div class="min-h-0 flex-1 overflow-auto p-6">

                <div class="overflow-x-auto rounded-lg border border-gray-200">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-100">
                            <tr>
                                <th
                                    class="w-24 px-4 py-3 text-left
                                           text-xs font-semibold uppercase
                                           text-gray-600"
                                >
                                    No
                                </th>

                                <th
                                    class="min-w-64 px-4 py-3 text-left
                                           text-xs font-semibold uppercase
                                           text-gray-600"
                                >
                                    Option
                                </th>

                                <th
                                    class="w-40 px-4 py-3 text-center
                                           text-xs font-semibold uppercase
                                           text-gray-600"
                                >
                                    Child Jawaban
                                </th>

                                <th
                                    class="min-w-64 px-4 py-3 text-left
                                           text-xs font-semibold uppercase
                                           text-gray-600"
                                >
                                    Label Child
                                </th>

                                <th
                                    class="w-20 px-4 py-3 text-center
                                           text-xs font-semibold uppercase
                                           text-gray-600"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody
                            id="createOptionBody"
                            class="divide-y divide-gray-200 bg-white"
                        ></tbody>

                    </table>

                </div>


                <button
                    type="button"
                    id="addOptionRow"
                    class="mt-4 inline-flex items-center gap-2
                           rounded-lg border border-indigo-600
                           px-4 py-2 text-sm font-medium
                           text-indigo-600 hover:bg-indigo-50"
                >
                    <i class="fa-solid fa-plus"></i>

                    Tambah Baris
                </button>

            </div>


            {{-- Footer --}}
            <div
                class="flex justify-end gap-3
                       border-t border-gray-200 px-6 py-4"
            >
                <button
                    type="button"
                    data-modal-close="createOptionModal"
                    class="rounded-lg border border-gray-300
                           px-4 py-2 text-sm font-medium
                           text-gray-700 hover:bg-gray-50"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="rounded-lg bg-indigo-600
                           px-4 py-2 text-sm font-medium
                           text-white hover:bg-indigo-700"
                >
                    Simpan Option
                </button>
            </div>

        </form>

    </div>
</div>