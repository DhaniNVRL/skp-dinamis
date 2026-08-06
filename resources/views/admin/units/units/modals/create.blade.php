<div
    id="createUnitModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center
           bg-black/50 p-4 sm:p-6"
>
    <div
        class="flex max-h-[90vh] w-full max-w-5xl
               flex-col overflow-hidden rounded-xl
               bg-white shadow-2xl"
    >

        {{-- HEADER --}}
        <div
            class="flex items-center justify-between
                   border-b border-gray-200 px-5 py-4 sm:px-6"
        >
            <div>
                <h2 class="text-xl font-semibold text-gray-900">
                    Tambah Unit
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Tambahkan satu atau beberapa unit sekaligus.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="createUnitModal"
                class="inline-flex h-9 w-9 items-center justify-center
                       rounded-lg text-gray-400 transition
                       hover:bg-gray-100 hover:text-red-600"
                aria-label="Tutup modal"
            >
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        {{-- FORM --}}
        <form
            action="{{ route('units.store') }}"
            method="POST"
            class="flex min-h-0 flex-1 flex-col"
        >
            @csrf

            <input
                type="hidden"
                name="group_id"
                value="{{ $groups->id }}"
            >

            {{-- BODY --}}
            <div class="min-h-0 flex-1 overflow-auto px-5 py-5 sm:px-6">

                <div
                    class="overflow-hidden rounded-lg
                           border border-gray-200"
                >
                    <table class="w-full border-collapse">

                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="w-16 border-b border-r
                                           border-gray-200 px-4 py-3
                                           text-center text-xs font-semibold
                                           uppercase tracking-wide text-gray-600"
                                >
                                    No
                                </th>

                                <th
                                    class="border-b border-r
                                           border-gray-200 px-4 py-3
                                           text-left text-xs font-semibold
                                           uppercase tracking-wide text-gray-600"
                                >
                                    Nama Unit
                                </th>

                                <th
                                    class="w-20 border-b border-gray-200
                                           px-4 py-3 text-center text-xs
                                           font-semibold uppercase
                                           tracking-wide text-gray-600"
                                >
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody
                            id="createUnitBody"
                            class="divide-y divide-gray-200 bg-white"
                        >
                        </tbody>

                    </table>
                </div>

                <p class="mt-3 text-xs text-gray-500">
                    Gunakan tombol Tambah Baris untuk memasukkan lebih dari satu unit.
                </p>
            </div>

            {{-- FOOTER --}}
            <div
                class="flex flex-col gap-3 border-t border-gray-200
                       bg-gray-50 px-5 py-4 sm:flex-row
                       sm:items-center sm:justify-between sm:px-6"
            >
                <button
                    id="addUnitRow"
                    type="button"
                    class="inline-flex items-center justify-center gap-2
                           rounded-lg border border-green-600
                           bg-white px-4 py-2 text-sm font-medium
                           text-green-600 transition
                           hover:bg-green-50"
                >
                    <i class="fa-solid fa-plus"></i>
                    Tambah Baris
                </button>

                <div class="flex items-center justify-end gap-2">
                    <button
                        type="button"
                        data-modal-close="createUnitModal"
                        class="rounded-lg border border-gray-300
                               bg-white px-4 py-2 text-sm font-medium
                               text-gray-700 transition hover:bg-gray-100"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-lg
                               bg-blue-600 px-5 py-2 text-sm font-medium
                               text-white transition hover:bg-blue-700"
                    >
                        <i class="fa-solid fa-floppy-disk"></i>
                        Simpan
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>