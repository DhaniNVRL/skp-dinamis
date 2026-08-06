<div
    id="createDescriptionModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-6"
>
    <div
        class="flex max-h-[90vh] w-full max-w-5xl
               flex-col overflow-hidden rounded-xl bg-white shadow-xl"
    >

        {{-- HEADER --}}
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">

            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Add Description
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Tambahkan description untuk form
                    <span
                        id="create_description_form_name"
                        class="font-medium text-gray-700"
                    ></span>.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="createDescriptionModal"
                class="text-gray-400 transition hover:text-red-600"
            >
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>

        </div>

        {{-- FORM --}}
        <form
            id="createDescriptionForm"
            action="{{ route('description.store') }}"
            method="POST"
        >
            @csrf

            <input
                type="hidden"
                name="group_id"
                id="create_description_group_id"
            >

            <input
                type="hidden"
                name="form_id"
                id="create_description_form_id"
            >

            @include(
                'admin.units.question.partials.description-editor',
                [
                    'editorId' => 'create_description_editor',
                    'contentInputId' => 'create_description_content',
                ]
            )

            <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4">

                <button
                    type="button"
                    data-modal-close="createDescriptionModal"
                    class="rounded-lg border border-gray-300
                        px-4 py-2 text-sm text-gray-700"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-4 py-2
                        text-sm font-medium text-white
                        hover:bg-blue-700"
                >
                    <i class="fa-solid fa-floppy-disk mr-1"></i>
                    Simpan
                </button>

            </div>
        </form>

    </div>
</div>