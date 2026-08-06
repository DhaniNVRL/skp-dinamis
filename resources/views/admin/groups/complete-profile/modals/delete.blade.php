<div
    id="deleteCprofileModal"
    data-modal
    class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h2 class="text-lg font-semibold mb-4">
            Hapus Data
        </h2>
        <p class="text-gray-600 mb-6">
            Apakah Anda yakin ingin menghapus data ini?
            <strong id="deleteCprofileName"></strong> ?
        </p>
        <form
            id="deleteForm"
            method="POST">
            @csrf
            @method('DELETE')
            <div class="flex justify-end gap-2">
                <button
                    type="button"
                    data-modal-close="deleteCprofileModal"
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
