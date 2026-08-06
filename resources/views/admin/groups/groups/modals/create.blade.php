<div
    id="createGroupModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-6"
>
    <div class="w-full max-w-7xl overflow-hidden rounded-xl bg-white shadow-xl">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">

            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Tambah Group
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Tambahkan satu atau beberapa group sekaligus.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="createGroupModal"
                class="text-gray-400 transition hover:text-red-600"
            >
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>

        </div>


        {{-- Form --}}
        <form
            action="{{ route('groups.store') }}"
            method="POST"
        >
            @csrf

            <input
                type="hidden"
                name="id_activities"
                value="{{ $activity->id }}"
            >


            {{-- Body --}}
            <div class="overflow-x-auto p-6">

                <div class="overflow-hidden rounded-lg border border-gray-200">

                    <table
                        id="groupCreateTable"
                        data-dynamic-table
                        data-template="createGroupRowTemplate"
                        class="w-full border-collapse"
                    >

                        <thead class="bg-gray-100">

                            <tr>

                                <th
                                    class="w-20 border-b border-r border-gray-200 px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600"
                                >
                                    No
                                </th>

                                <th
                                    class="border-b border-r border-gray-200 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600"
                                >
                                    Nama Group
                                </th>

                                <th
                                    class="w-20 border-b border-gray-200 px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600"
                                >
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody
                            id="groupCreateBody"
                            class="divide-y divide-gray-200"
                        >
                        </tbody>

                    </table>

                </div>

            </div>


            {{-- Footer --}}
            <div class="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-6 py-4">

                <button
                    type="button"
                    data-add-row="groupCreateBody"
                    class="inline-flex items-center gap-2 rounded-lg border border-green-600 px-4 py-2 text-green-600 transition hover:bg-green-50"
                >
                    <i class="fa-solid fa-plus"></i>

                    Tambah Baris
                </button>


                <div class="flex items-center gap-2">

                    <button
                        type="button"
                        data-modal-close="createGroupModal"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-100"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-2 text-white transition hover:bg-blue-700"
                    >
                        <i class="fa-solid fa-floppy-disk mr-1"></i>

                        Simpan
                    </button>

                </div>

            </div>

        </form>

    </div>
</div>
