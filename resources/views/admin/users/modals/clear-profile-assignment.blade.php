<div id="clearProfileAssignmentModal" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-lg overflow-hidden rounded-xl bg-white shadow-xl">
        <form id="clearProfileAssignmentForm" method="POST" action="#">
            @csrf

            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Hapus Group dan Unit Profile</h3>
                    <p class="mt-1 text-sm text-gray-500">Tinjau dampak sebelum melanjutkan.</p>
                </div>
                <button type="button" data-modal-close="clearProfileAssignmentModal" class="text-gray-400 hover:text-red-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <div class="p-6">
                <p class="text-sm leading-6 text-gray-700">
                    Apakah Anda yakin ingin menghapus Group dan Unit dari profile
                    <strong id="clearProfileAssignmentName" class="text-gray-900">user ini</strong>?
                </p>

                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-800">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-triangle-exclamation mt-1"></i>
                        <div>
                            <p class="font-semibold">Data yang dikosongkan:</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                <li>Group</li>
                                <li>Unit</li>
                            </ul>
                            <p class="mt-3">Activity, jawaban, progres dan status survey, akun, serta data profile lainnya tetap dipertahankan.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4">
                <button type="button" data-modal-close="clearProfileAssignmentModal" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    Batal
                </button>
                <button type="submit" class="rounded-lg bg-fuchsia-600 px-4 py-2 text-sm font-medium text-white hover:bg-fuchsia-700">
                    <i class="fa-solid fa-user-minus mr-2"></i>Hapus Group dan Unit
                </button>
            </div>
        </form>
    </div>
</div>