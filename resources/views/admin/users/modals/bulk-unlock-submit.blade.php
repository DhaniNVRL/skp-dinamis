<div id="bulkUnlockSubmitModal" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-lg overflow-hidden rounded-xl bg-white shadow-xl">
        <form method="POST" action="{{ route('admin.datauser.bulk-unlock-submit') }}">
            @csrf
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Unlock Submit Terpilih</h3>
                    <p class="mt-1 text-sm text-gray-500"><span id="bulkUnlockSubmitCount">0</span> akun akan diperiksa.</p>
                </div>
                <button type="button" data-modal-close="bulkUnlockSubmitModal" class="text-gray-400 hover:text-red-600"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <div id="bulkUnlockSubmitIds"></div>
            <div class="space-y-4 p-6 text-sm text-gray-700">
                <p>Akun yang surveinya sudah selesai akan dibuka kembali dari form pertama.</p>
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                    <p class="font-semibold"><i class="fa-solid fa-lock-open mr-2"></i>Jawaban tetap aman.</p>
                    <p class="mt-1">Akun yang belum selesai atau konfigurasi formnya belum lengkap akan dilewati dan dilaporkan.</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4">
                <button type="button" data-modal-close="bulkUnlockSubmitModal" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Batal</button>
                <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700"><i class="fa-solid fa-lock-open mr-2"></i>Ya, Buka Akses</button>
            </div>
        </form>
    </div>
</div>
