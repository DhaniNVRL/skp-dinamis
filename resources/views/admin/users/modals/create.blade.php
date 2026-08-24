<div
    id="createUserModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-2 sm:p-4"
>
    <div class="max-h-[90vh] w-[calc(100vw-1rem)] max-w-[1800px] overflow-y-auto rounded-xl bg-white shadow-xl">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">

            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Tambah Akun
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Tambahkan satu atau beberapa user sekaligus.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="createUserModal"
                class="text-gray-400 transition hover:text-red-600"
            >
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>

        </div>


        {{-- Form --}}
        <form
            action="{{ route('admin.datauser.store') }}"
            method="POST"
        >
            @csrf


            {{-- Body --}}
            <div class="max-w-full p-6">

                <div class="max-w-full overflow-x-scroll rounded-lg border border-gray-200 pb-2">

                    <table
                        id="userCreateTable"
                        data-dynamic-table
                        data-template="createUserRowTemplate"
                        class="min-w-[1500px] w-full border-collapse"
                    >

                        <thead class="bg-gray-100">

                            <tr>

                                <th
                                    class="w-20 border-b border-r border-gray-200 px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600"
                                >
                                    No
                                </th>

                                <th
                                    class="min-w-64 border-b border-r border-gray-200 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600"
                                >
                                    Username
                                </th>

                                <th
                                    class="min-w-64 border-b border-r border-gray-200 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600"
                                >
                                    Password
                                </th>

                                <th
                                    class="min-w-52 border-b border-r border-gray-200 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600"
                                >
                                    Role
                                </th>

                                <th
                                    class="min-w-64 border-b border-r border-gray-200 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600"
                                >
                                    Activity
                                </th>
                                <th class="min-w-64 border-b border-r border-gray-200 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                                    Group <span class="normal-case text-gray-400">(opsional)</span>
                                </th>

                                <th class="min-w-64 border-b border-r border-gray-200 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                                    Unit <span class="normal-case text-gray-400">(opsional)</span>
                                </th>

                                <th
                                    class="w-20 border-b border-gray-200 px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600"
                                >
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody
                            id="userCreateBody"
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
                    data-add-row="userCreateBody"
                    class="inline-flex items-center gap-2 rounded-lg border border-green-600 px-4 py-2 text-green-600 transition hover:bg-green-50"
                >
                    <i class="fa-solid fa-plus"></i>

                    Tambah Baris
                </button>


                <div class="flex items-center gap-2">

                    <button
                        type="button"
                        data-modal-close="createUserModal"
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
