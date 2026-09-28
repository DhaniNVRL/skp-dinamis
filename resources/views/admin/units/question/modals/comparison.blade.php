<div id="comparisonModal" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-2xl rounded-xl bg-white shadow-xl">
        <div class="flex items-start justify-between border-b border-gray-200 px-6 py-4"><div><h2 class="text-lg font-semibold text-gray-800">Input Pembanding</h2><p class="mt-1 text-sm text-gray-500">Atur pertanyaan dan pilihan radio pembanding tahun.</p></div><button type="button" data-modal-close="comparisonModal" class="h-9 w-9 rounded-lg text-gray-500 hover:bg-gray-100"><i class="fa-solid fa-xmark"></i></button></div>
        <form id="comparisonForm" method="POST">@csrf @method('PUT')
            <div class="space-y-5 p-6">
                <label class="flex items-center gap-2 font-medium text-gray-800"><input id="comparison_enabled" name="comparison_enabled" type="checkbox" value="1" class="rounded border-gray-300 text-violet-600"> Aktifkan pembanding tahun</label>
                <div id="comparisonFields" class="space-y-4">
                    <div><label for="comparison_prompt" class="mb-2 block text-sm font-medium text-gray-700">Pertanyaan Pembanding</label><textarea id="comparison_prompt" name="comparison_prompt" rows="3" maxlength="1000" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea></div>
                    <div>
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Pilihan Radio</p>
                                <p class="mt-1 text-xs text-gray-500">Satu kolom digunakan untuk satu pilihan jawaban.</p>
                            </div>
                            <button id="addComparisonOption" type="button" class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-violet-200 bg-violet-50 px-3 py-2 text-sm font-medium text-violet-700 hover:bg-violet-100">
                                <i class="fa-solid fa-plus"></i> Tambah
                            </button>
                        </div>
                        <input id="comparison_options" name="comparison_options" type="hidden">
                        <div id="comparisonOptionRows" class="space-y-3"></div>
                        <template id="comparisonOptionRowTemplate">
                            <div data-comparison-option-row class="flex items-end gap-2">
                                <div class="min-w-0 flex-1">
                                    <label data-comparison-option-label class="mb-1.5 block text-sm font-medium text-gray-700"></label>
                                    <input data-comparison-option-input type="text" maxlength="500" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-violet-500 focus:ring-violet-500" placeholder="Masukkan pilihan radio">
                                </div>
                                <button data-remove-comparison-option type="button" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100" title="Hapus pilihan">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4"><button type="button" data-modal-close="comparisonModal" class="rounded-lg border border-gray-300 px-4 py-2 text-sm">Batal</button><button type="submit" class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white">Simpan Pembanding</button></div>
        </form>
    </div>
</div>
