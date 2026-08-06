<div
    id="importQuestionModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center
           bg-black/50 p-6"
>
    <div class="w-full max-w-lg overflow-hidden rounded-xl bg-white shadow-xl">

        {{-- HEADER --}}
        <div class="flex items-start justify-between border-b border-gray-200 px-6 py-4">

            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Import Pertanyaan
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Upload pertanyaan menggunakan template Excel.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="importQuestionModal"
                class="text-gray-400 transition hover:text-red-600"
            >
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>

        </div>


        {{-- FORM IMPORT --}}
        <form
            id="importQuestionForm"
            method="POST"
            enctype="multipart/form-data"
            action="{{
                old('form_id')
                    ? route('question.import', [
                        'formId' => old('form_id'),
                    ])
                    : ''
            }}"
        >
            @csrf

            {{-- HIDDEN DATA --}}
            <input
                type="hidden"
                name="group_id"
                id="importQuestionGroupId"
                value="{{ old('group_id') }}"
            >

            <input
                type="hidden"
                name="form_id"
                id="importQuestionFormId"
                value="{{ old('form_id') }}"
            >


            {{-- BODY --}}
            <div class="space-y-5 px-6 py-5">

                {{-- INFORMASI FORM --}}
                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">

                    <div class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                        Form tujuan
                    </div>

                    <div
                        id="importQuestionFormName"
                        class="mt-1 font-semibold text-blue-900"
                    >
                        -
                    </div>

                </div>


                {{-- PESAN ERROR --}}
                @if (
                    $errors->has('file')
                    || $errors->has('group_id')
                    || $errors->has('form_id')
                )

                    <div class="rounded-lg border border-red-200 bg-red-50 p-4">

                        <div class="flex gap-3">

                            <i class="fa-solid fa-circle-exclamation mt-0.5 text-red-500"></i>

                            <div>
                                <p class="text-sm font-semibold text-red-700">
                                    Import tidak dapat diproses
                                </p>

                                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-600">

                                    @foreach ($errors->all() as $error)
                                        <li>
                                            {{ $error }}
                                        </li>
                                    @endforeach

                                </ul>
                            </div>

                        </div>

                    </div>

                @endif


                {{-- FILE INPUT --}}
                <div>

                    <label
                        for="importQuestionFile"
                        class="mb-2 block text-sm font-medium text-gray-700"
                    >
                        File Excel

                        <span class="text-red-500">
                            *
                        </span>
                    </label>

                    <label
                        for="importQuestionFile"
                        class="flex cursor-pointer flex-col items-center justify-center
                               rounded-xl border-2 border-dashed border-gray-300
                               bg-gray-50 px-6 py-8 text-center
                               transition hover:border-blue-400 hover:bg-blue-50"
                    >
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-blue-500"></i>

                        <span class="mt-3 text-sm font-medium text-gray-700">
                            Pilih file Excel
                        </span>

                        <span class="mt-1 text-xs text-gray-500">
                            Format file: XLSX atau XLS
                        </span>

                        <span
                            id="importQuestionFileName"
                            class="mt-3 hidden rounded-lg bg-white px-3 py-2
                                   text-sm font-medium text-blue-700 shadow-sm"
                        ></span>
                    </label>

                    <input
                        type="file"
                        name="file"
                        id="importQuestionFile"
                        accept=".xlsx,.xls"
                        required
                        class="hidden"
                    >

                </div>


                {{-- PERINGATAN --}}
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">

                    <div class="flex gap-3">

                        <i class="fa-solid fa-triangle-exclamation mt-0.5 text-amber-500"></i>

                        <div class="text-sm text-amber-800">

                            <p class="font-medium">
                                Pastikan menggunakan template dari form ini.
                            </p>

                            <p class="mt-1 text-xs leading-5">
                                Jangan mengubah nama sheet dan judul kolom.
                                Pertanyaan dan option akan disimpan dalam satu proses.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            {{-- FOOTER --}}
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">

                <button
                    type="button"
                    data-modal-close="importQuestionModal"
                    class="rounded-lg border border-gray-300 px-4 py-2
                           text-sm font-medium text-gray-700
                           transition hover:bg-gray-100"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    id="importQuestionSubmitButton"
                    class="inline-flex items-center gap-2 rounded-lg
                           bg-blue-600 px-4 py-2 text-sm font-medium
                           text-white transition hover:bg-blue-700"
                >
                    <i class="fa-solid fa-file-import"></i>
                    Import Pertanyaan
                </button>

            </div>

        </form>

    </div>
</div>


@push('scripts')

    <script>
        document.addEventListener("click", function (event) {
            const button = event.target.closest(
                '[data-modal-open="importQuestionModal"]'
            );

            if (!button) {
                return;
            }

            const importForm = document.getElementById(
                "importQuestionForm"
            );

            const groupInput = document.getElementById(
                "importQuestionGroupId"
            );

            const formInput = document.getElementById(
                "importQuestionFormId"
            );

            const formName = document.getElementById(
                "importQuestionFormName"
            );

            const fileInput = document.getElementById(
                "importQuestionFile"
            );

            const fileName = document.getElementById(
                "importQuestionFileName"
            );

            const submitButton = document.getElementById(
                "importQuestionSubmitButton"
            );

            if (importForm) {
                importForm.action =
                    button.dataset.action || "";
            }

            if (groupInput) {
                groupInput.value =
                    button.dataset.groupId || "";
            }

            if (formInput) {
                formInput.value =
                    button.dataset.formId || "";
            }

            if (formName) {
                formName.textContent =
                    button.dataset.formName || "-";
            }

            if (fileInput) {
                fileInput.value = "";
            }

            if (fileName) {
                fileName.textContent = "";
                fileName.classList.add("hidden");
            }

            if (submitButton) {
                submitButton.disabled = false;

                submitButton.classList.remove(
                    "cursor-not-allowed",
                    "opacity-60"
                );

                submitButton.innerHTML = `
                    <i class="fa-solid fa-file-import"></i>
                    Import Pertanyaan
                `;
            }
        });


        /*
        |--------------------------------------------------------------------------
        | Tampilkan nama file yang dipilih
        |--------------------------------------------------------------------------
        */
        document.addEventListener("change", function (event) {
            if (event.target.id !== "importQuestionFile") {
                return;
            }

            const fileInput = event.target;

            const fileName = document.getElementById(
                "importQuestionFileName"
            );

            if (!fileName) {
                return;
            }

            const selectedFile = fileInput.files[0];

            if (!selectedFile) {
                fileName.textContent = "";
                fileName.classList.add("hidden");

                return;
            }

            fileName.textContent = selectedFile.name;
            fileName.classList.remove("hidden");
        });


        /*
        |--------------------------------------------------------------------------
        | Loading ketika submit
        |--------------------------------------------------------------------------
        */
        document.addEventListener("submit", function (event) {
            if (event.target.id !== "importQuestionForm") {
                return;
            }

            const submitButton = document.getElementById(
                "importQuestionSubmitButton"
            );

            if (!submitButton) {
                return;
            }

            submitButton.disabled = true;

            submitButton.classList.add(
                "cursor-not-allowed",
                "opacity-60"
            );

            submitButton.innerHTML = `
                <i class="fa-solid fa-spinner fa-spin"></i>
                Memproses Import...
            `;
        });


        /*
        |--------------------------------------------------------------------------
        | Reset tombol ketika modal ditutup
        |--------------------------------------------------------------------------
        */
        document.addEventListener("click", function (event) {
            const closeButton = event.target.closest(
                '[data-modal-close="importQuestionModal"]'
            );

            if (!closeButton) {
                return;
            }

            const submitButton = document.getElementById(
                "importQuestionSubmitButton"
            );

            if (!submitButton) {
                return;
            }

            submitButton.disabled = false;

            submitButton.classList.remove(
                "cursor-not-allowed",
                "opacity-60"
            );

            submitButton.innerHTML = `
                <i class="fa-solid fa-file-import"></i>
                Import Pertanyaan
            `;
        });


        /*
        |--------------------------------------------------------------------------
        | Buka kembali modal jika validasi import gagal
        |--------------------------------------------------------------------------
        */
        @if (
            $errors->has('file')
            || $errors->has('group_id')
            || $errors->has('form_id')
        )

            document.addEventListener(
                "DOMContentLoaded",
                function () {
                    const oldFormId = @json(
                        old('form_id')
                    );

                    if (!oldFormId) {
                        return;
                    }

                    const importButton =
                        document.querySelector(
                            `[data-modal-open="importQuestionModal"]` +
                            `[data-form-id="${oldFormId}"]`
                        );

                    if (importButton) {
                        importButton.click();
                    }
                }
            );

        @endif
    </script>

@endpush