<div
    id="editQuestionModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4"
>
    <div class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-xl bg-white shadow-xl">

        {{-- Header --}}
        <div class="flex items-start justify-between border-b border-gray-200 px-6 py-4">

            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Edit Pertanyaan
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Perbarui informasi dan tipe pertanyaan.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="editQuestionModal"
                class="flex h-9 w-9 items-center justify-center
                       rounded-lg text-gray-500 hover:bg-gray-100"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>

        {{-- Form --}}
        <form
            id="editQuestionForm"
            method="POST"
            class="flex min-h-0 flex-1 flex-col"
        >
            @csrf
            @method('PUT')

            <input
                type="hidden"
                name="question_id"
                id="edit_question_id"
            >

            <input
                type="hidden"
                name="group_id"
                id="edit_question_group_id"
            >

            <input
                type="hidden"
                name="form_id"
                id="edit_question_form_id"
            >

            <input
                type="hidden"
                name="formtype_id"
                id="edit_question_formtype_id"
            >

            {{-- Body --}}
            <div class="min-h-0 flex-1 overflow-y-auto p-6">

                <div class="grid grid-cols-1 gap-5 md:grid-cols-12">

                    {{-- Header --}}
                    <div class="md:col-span-3">
                        <label
                            for="edit_question_header"
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Header
                        </label>

                        <input
                            type="text"
                            name="no_header"
                            id="edit_question_header"
                            maxlength="20"
                            placeholder="A"
                            class="w-full rounded-lg border border-gray-300
                                   px-3 py-2 text-sm
                                   focus:border-indigo-500 focus:outline-none
                                   focus:ring-1 focus:ring-indigo-500"
                        >

                        @error('no_header')
                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Nomor --}}
                    <div class="md:col-span-3">
                        <label
                            for="edit_question_no"
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Nomor
                        </label>

                        <input
                            type="number"
                            name="no"
                            id="edit_question_no"
                            min="1"
                            required
                            placeholder="1"
                            class="w-full rounded-lg border border-gray-300
                                   px-3 py-2 text-sm
                                   focus:border-indigo-500 focus:outline-none
                                   focus:ring-1 focus:ring-indigo-500"
                        >

                        @error('no')
                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Tipe pertanyaan --}}
                    <div class="md:col-span-6">
                        <label
                            for="edit_question_type"
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Tipe Pertanyaan
                        </label>

                        <select
                            name="questiontype_id"
                            id="edit_question_type"
                            required
                            class="w-full rounded-lg border border-gray-300
                                   px-3 py-2 text-sm
                                   focus:border-indigo-500 focus:outline-none
                                   focus:ring-1 focus:ring-indigo-500"
                        >
                            <option value="">
                                Pilih tipe
                            </option>
                        </select>

                        <p
                            id="edit_question_type_help"
                            class="mt-1 text-xs leading-5 text-gray-500"
                        ></p>

                        @error('questiontype_id')
                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Pertanyaan --}}
                    <div class="md:col-span-12">
                        <label
                            for="edit_question_name"
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Pertanyaan
                        </label>

                        <textarea
                            name="name"
                            id="edit_question_name"
                            rows="5"
                            required
                            placeholder="Masukkan pertanyaan"
                            class="w-full resize-none rounded-lg
                                   border border-gray-300 px-3 py-2 text-sm
                                   focus:border-indigo-500 focus:outline-none
                                   focus:ring-1 focus:ring-indigo-500"
                        ></textarea>

                        @error('name')
                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

                {{-- Peringatan perubahan tipe --}}
                <div
                    id="edit_question_type_warning"
                    class="mt-5 hidden rounded-lg border border-amber-200
                           bg-amber-50 p-4"
                >
                    <div class="flex items-start gap-3">

                        <i class="fa-solid fa-triangle-exclamation mt-0.5 text-amber-600"></i>

                        <div>
                            <p class="text-sm font-semibold text-amber-800">
                                Tipe pertanyaan diubah
                            </p>

                            <p class="mt-1 text-xs leading-5 text-amber-700">
                                Perubahan tipe dapat memengaruhi pilihan jawaban
                                yang sudah terhubung dengan pertanyaan ini.
                            </p>
                        </div>

                    </div>
                </div>

                <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-triangle-exclamation mt-0.5 text-red-600"></i>
                        <div>
                            <p class="text-sm font-semibold text-red-800">Jawaban responden akan dihapus</p>
                            <p class="mt-1 text-xs leading-5 text-red-700">Saat perubahan disimpan, seluruh jawaban yang terhubung dengan ID pertanyaan ini akan dihapus permanen. Akun dan jawaban untuk pertanyaan lain tidak terpengaruh.</p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4">

                <button
                    type="button"
                    data-modal-close="editQuestionModal"
                    class="rounded-lg border border-gray-300 px-4 py-2
                           text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="rounded-lg bg-indigo-600 px-4 py-2
                           text-sm font-medium text-white hover:bg-indigo-700"
                >
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>
</div>
