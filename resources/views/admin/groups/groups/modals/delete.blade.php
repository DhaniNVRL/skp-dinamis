<div
    id="deleteGroupModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-6">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h2 class="text-lg font-semibold mb-4">
            Hapus Group
        </h2>
        <p class="text-gray-600 mb-6">
            Apakah Anda yakin ingin menghapus data ini?
            <strong id="deleteGroupName"></strong> ?
        </p>
        <form
            id="deleteGroupForm"
            method="POST">
            @csrf
            @method('DELETE')
            <div class="flex justify-end gap-2">
                <button
                    type="button"
                    data-modal-close="deleteGroupModal"
                    class="px-4 py-2 rounded bg-gray-300">
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 rounded bg-red-600 text-white">
                    Hapus
                </button>
            </div>
        </form>
    </div>
</div>
