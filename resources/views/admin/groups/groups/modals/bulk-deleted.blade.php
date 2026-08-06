<div
    id="bulkDeleteModal"
    data-modal
    class="fixed inset-0 hidden items-center justify-center bg-black/50">

    <div class="bg-white rounded-lg w-full max-w-md p-6">

        <h2 class="text-lg font-semibold">
            Hapus Data Group
        </h2>

        <p class="mt-2">
            akan dihapus
            <strong id="bulkDeleteCount">0</strong>
            Groups.
        </p>

        <ul
            id="bulkDeleteUserList"
            class="mt-3 list-disc list-inside text-sm text-gray-700 max-h-48 overflow-y-auto">
        </ul>


        <form
            id="bulkDeleteForm"
            method="POST"
            action="{{ route('groups.bulkDelete') }}">

            @csrf
            @method('DELETE')
            <div id="bulkDeleteInputs"></div>

            <div class="mt-6 flex justify-end gap-2">

                <button
                    type="button"
                    data-modal-close="bulkDeleteModal"
                    class="px-4 py-2 rounded border">
                    Cancel
                </button>


                <button
                    type="submit"
                    class="bg-red-600 text-white px-4 py-2 rounded">
                    Delete
                </button>

            </div>

        </form>

    </div>

</div>
