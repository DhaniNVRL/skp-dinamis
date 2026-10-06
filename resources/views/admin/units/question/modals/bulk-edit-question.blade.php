<div id="bulkEditQuestionModal" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="flex max-h-[92vh] w-full max-w-6xl flex-col overflow-hidden rounded-xl bg-white shadow-xl">
        <div class="flex items-start justify-between border-b border-gray-200 px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Edit Pertanyaan Terpilih</h2>
                <p class="mt-1 text-sm text-gray-500">Ubah header, nomor, dan narasi beberapa pertanyaan sekaligus.</p>
            </div>
            <button type="button" data-modal-close="bulkEditQuestionModal" class="flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="bulkEditQuestionForm" method="POST" class="flex min-h-0 flex-1 flex-col">
            @csrf
            @method('PUT')
            <input id="bulkEditQuestionFormId" type="hidden" name="form_id">

            <div class="min-h-0 flex-1 overflow-auto p-6">
                <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                    <i class="fa-solid fa-circle-info mr-2"></i>
                    Tipe jawaban, option, dan jawaban responden tidak berubah.
                </div>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100 text-left text-xs font-semibold uppercase text-gray-600">
                            <tr>
                                <th class="w-16 px-4 py-3">No.</th>
                                <th class="w-32 px-4 py-3">Header</th>
                                <th class="w-32 px-4 py-3">Nomor</th>
                                <th class="min-w-96 px-4 py-3">Pertanyaan</th>
                            </tr>
                        </thead>
                        <tbody id="bulkEditQuestionBody" class="divide-y divide-gray-200 bg-white"></tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4">
                <button type="button" data-modal-close="bulkEditQuestionModal" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Semua Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
