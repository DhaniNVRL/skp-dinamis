<div id="bulkDeleteModal" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md overflow-hidden rounded-xl bg-white shadow-xl">
        <form id="bulkDeleteForm" method="POST" action="{{ route('admin.datauser.bulk-delete') }}">
            @csrf
            @method('DELETE')

            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Hapus Akun Terpilih</h3>
                    <p class="mt-1 text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <button type="button" data-modal-close="bulkDeleteModal" class="text-gray-400 hover:text-red-600"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>

            <div class="p-6">
                <p class="text-sm text-gray-700">Yakin ingin menghapus <strong id="bulkDeleteCount">0</strong> akun?</p>
                <ul id="bulkDeleteUserList" class="mt-3 max-h-48 list-disc space-y-1 overflow-y-auto pl-5 text-sm text-gray-600"></ul>
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4">
                <button type="button" data-modal-close="bulkDeleteModal" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Batal</button>
                <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"><i class="fa-solid fa-trash mr-2"></i>Hapus</button>
            </div>
        </form>
    </div>
</div>
