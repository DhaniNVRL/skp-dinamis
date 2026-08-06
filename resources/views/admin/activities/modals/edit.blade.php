<div
    id="editActivityModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-6"
>
    <div class="w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-xl">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">

            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Edit Activity
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Ubah informasi activity yang dipilih.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="editActivityModal"
                class="text-gray-400 transition hover:text-red-600"
            >
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>

        </div>


        <form
            id="editActivityForm"
            method="POST"
        >
            @csrf
            @method('PUT')


            {{-- ID --}}
            <input
                type="hidden"
                name="id"
                id="edit_activity_id"
            >


            {{-- Body --}}
            <div class="space-y-5 p-6">

                {{-- ACTIVITY NAME --}}
                <div>

                    <label
                        for="edit_activity_name"
                        class="mb-2 block text-sm font-medium text-gray-700"
                    >
                        Nama Activity
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="edit_activity_name"
                        required
                        autocomplete="off"
                        placeholder="Nama activity"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >

                </div>


                {{-- DESCRIPTION --}}
                <div>

                    <label
                        for="edit_activity_description"
                        class="mb-2 block text-sm font-medium text-gray-700"
                    >
                        Deskripsi
                    </label>

                    <textarea
                        name="description"
                        id="edit_activity_description"
                        rows="4"
                        placeholder="Deskripsi activity"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                    ></textarea>

                </div>

            </div>


            {{-- Footer --}}
            <div class="flex items-center justify-end gap-2 border-t border-gray-200 bg-gray-50 px-6 py-4">

                <button
                    type="button"
                    data-modal-close="editActivityModal"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700
                           transition hover:bg-gray-100"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-2 text-white
                           transition hover:bg-blue-700"
                >
                    <i class="fa-solid fa-floppy-disk mr-1"></i>
                    Simpan
                </button>

            </div>

        </form>

    </div>
</div>
