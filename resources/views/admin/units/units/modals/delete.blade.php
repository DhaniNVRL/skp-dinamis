<div
    id="deleteUnitModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-6"
>

    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">

        <h2 class="mb-4 text-lg font-semibold text-gray-800">
            Hapus Unit
        </h2>

        <p class="mb-6 text-gray-600">

            Apakah Anda yakin ingin menghapus Unit

            <strong
                id="deleteUnitName"
                class="text-gray-900">
            </strong>

            ?

        </p>


        <form
            id="deleteUnitForm"
            method="POST">

            @csrf
            @method('DELETE')


            <div class="flex justify-end gap-2">

                <button
                    type="button"
                    data-modal-close="deleteFormModal"
                    class="rounded bg-gray-300 px-4 py-2 hover:bg-gray-400"
                >
                    Batal
                </button>


                <button
                    type="submit"
                    class="rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700"
                >
                    Hapus
                </button>

            </div>

        </form>

    </div>

</div>