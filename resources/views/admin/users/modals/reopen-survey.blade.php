<div id="reopenSurveyModal" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-lg overflow-hidden rounded-xl bg-white shadow-xl">
        <form id="reopenSurveyForm" method="POST">
            @csrf

            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Buka Kembali Akses Survey</h3>
                    <p class="mt-1 text-sm text-gray-500">User dapat kembali masuk ke halaman pertanyaan.</p>
                </div>
                <button type="button" data-modal-close="reopenSurveyModal" class="text-gray-400 hover:text-red-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <div class="p-6">
                <p class="text-sm text-gray-700">
                    Apakah Anda yakin ingin membuka kembali akses survey untuk
                    <strong id="reopenSurveyUserName" class="text-gray-900"></strong>?
                </p>

                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-lock-open mt-0.5"></i>
                        <div>
                            <p class="font-semibold">Dampak pembukaan akses:</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                <li>Status survey berubah menjadi Sedang Mengisi.</li>
                                <li>User dapat mengakses kembali pertanyaan dari form pertama.</li>
                                <li>Jawaban dan profil yang sudah ada tetap dipertahankan.</li>
                                <li>Kolom Keterangan akan menampilkan �Akun Dibuka Kembali�.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4">
                <button type="button" data-modal-close="reopenSurveyModal" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Batal</button>
                <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                    <i class="fa-solid fa-lock-open mr-2"></i>Ya, Buka Akses
                </button>
            </div>
        </form>
    </div>
</div>
