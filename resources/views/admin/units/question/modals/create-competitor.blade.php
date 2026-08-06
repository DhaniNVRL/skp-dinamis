<div
    id="createCompetitorModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4"
>
    <div class="flex max-h-[90vh] w-full max-w-3xl flex-col overflow-hidden rounded-xl bg-white shadow-xl">

        <div class="flex items-start justify-between border-b border-gray-200 px-6 py-4">

            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Tambah Kompetitor
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Tambahkan satu atau beberapa kompetitor.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="createCompetitorModal"
                class="flex h-9 w-9 items-center justify-center
                       rounded-lg text-gray-500 hover:bg-gray-100"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>

        <form
            id="createCompetitorForm"
            method="POST"
            class="flex min-h-0 flex-1 flex-col"
        >
            @csrf

            <input
                type="hidden"
                name="group_id"
                id="create_competitor_group_id"
            >

            <input
                type="hidden"
                name="form_id"
                id="create_competitor_form_id"
            >

            <div class="min-h-0 flex-1 overflow-auto p-6">

                <div class="overflow-hidden rounded-lg border border-gray-200">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                                    Nama Kompetitor
                                </th>

                                <th class="w-20 px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody
                            id="createCompetitorBody"
                            class="divide-y divide-gray-200 bg-white"
                        ></tbody>

                    </table>

                </div>

                <button
                    type="button"
                    id="addCompetitorRow"
                    class="mt-4 inline-flex items-center gap-2
                           rounded-lg border border-violet-600
                           px-4 py-2 text-sm font-medium text-violet-600
                           hover:bg-violet-50"
                >
                    <i class="fa-solid fa-plus"></i>
                    Tambah Baris
                </button>

            </div>

            <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4">

                <button
                    type="button"
                    data-modal-close="createCompetitorModal"
                    class="rounded-lg border border-gray-300 px-4 py-2
                           text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="rounded-lg bg-violet-600 px-4 py-2
                           text-sm font-medium text-white hover:bg-violet-700"
                >
                    Simpan Kompetitor
                </button>

            </div>

        </form>

    </div>
</div>