<div id="bulkResetAccountModal" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-lg overflow-hidden rounded-xl bg-white shadow-xl">
        <form method="POST" action="{{ route('admin.datauser.bulk-reset-account') }}">
            @csrf
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Reset Account Terpilih</h3>
                    <p class="mt-1 text-sm text-gray-500"><span id="bulkResetAccountCount">0</span> akun akan di-reset.</p>
                </div>
                <button type="button" data-modal-close="bulkResetAccountModal" class="text-gray-400 hover:text-red-600"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <div id="bulkResetAccountIds"></div>
            <div class="space-y-4 p-6 text-sm text-gray-700">
                <p>Jawaban dan progres survei seluruh akun terpilih akan dihapus sehingga survei dapat dimulai kembali.</p>
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
                    <p class="font-semibold"><i class="fa-solid fa-triangle-exclamation mr-2"></i>Data jawaban tidak dapat dipulihkan.</p>
                    <p class="mt-1">Akun, role, Activity, Group, Unit, dan profil user tetap dipertahankan.</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4">
                <button type="button" data-modal-close="bulkResetAccountModal" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Batal</button>
                <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"><i class="fa-solid fa-rotate-left mr-2"></i>Ya, Reset Account</button>
            </div>
        </form>
    </div>
</div>
