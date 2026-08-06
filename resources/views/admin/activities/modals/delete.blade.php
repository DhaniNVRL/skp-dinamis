<div
    id="deleteModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4"
>
    <div class="w-full max-w-md overflow-hidden rounded-xl bg-white shadow-xl">
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')

            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Hapus Activity</h3>
                    <p class="mt-1 text-sm text-gray-500">Konfirmasi penghapusan data Activity.</p>
                </div>
                <button type="button" data-modal-close="deleteModal" class="text-gray-400 hover:text-red-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <div class="p-6 text-sm text-gray-600">
                Yakin ingin menghapus Activity <strong id="deleteUserName" class="text-gray-900"></strong>?
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4">
                <button type="button" data-modal-close="deleteModal" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Batal</button>
                <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                    <i class="fa-solid fa-trash mr-2"></i>Hapus
                </button>
            </div>
        </form>
    </div>
</div>
