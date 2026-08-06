<div
    id="editCprofileModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-6"
>
    <div class="w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-xl">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">

            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Edit Complete Profile
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Ubah pertanyaan Group dan Unit.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="editCprofileModal"
                class="text-gray-400 transition hover:text-red-600"
            >
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>

        </div>


        {{-- Form --}}
        <form
            id="editCprofileForm"
            method="POST"
        >
            @csrf
            @method('PUT')


            {{-- ID --}}
            <input
                type="hidden"
                name="id"
                id="edit_cprofile_id"
            >


            {{-- Body --}}
            <div class="space-y-5 p-6">

                {{-- PERTANYAAN GROUP --}}
                <div>

                    <label
                        for="edit_cprofile_pgroup"
                        class="mb-2 block text-sm font-medium text-gray-700"
                    >
                        Pertanyaan Group
                    </label>

                    <input
                        type="text"
                        name="pgroup"
                        id="edit_cprofile_pgroup"
                        required
                        autocomplete="off"
                        placeholder="Pertanyaan group"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >

                </div>


                {{-- PERTANYAAN UNIT --}}
                <div>

                    <label
                        for="edit_cprofile_punit"
                        class="mb-2 block text-sm font-medium text-gray-700"
                    >
                        Pertanyaan Unit
                    </label>

                    <input
                        type="text"
                        name="punit"
                        id="edit_cprofile_punit"
                        required
                        autocomplete="off"
                        placeholder="Pertanyaan unit"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >

                </div>

            </div>


            {{-- Footer --}}
            <div class="flex items-center justify-end gap-2 border-t border-gray-200 bg-gray-50 px-6 py-4">

                <button
                    type="button"
                    data-modal-close="editCprofileModal"
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
