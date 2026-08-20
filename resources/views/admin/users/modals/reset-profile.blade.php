<div id="resetProfileModal" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-lg overflow-hidden rounded-xl bg-white shadow-xl">
        <form id="resetProfileForm" method="POST">
            @csrf

            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Reset Profile User</h3>
                    <p class="mt-1 text-sm text-gray-500">Tinjau risiko sebelum melanjutkan.</p>
                </div>
                <button type="button" data-modal-close="resetProfileModal" class="text-gray-400 hover:text-red-600"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>

            <div class="p-6">
                <p class="text-sm text-gray-700">Reset profile <strong id="resetProfileUserName" class="text-gray-900"></strong>?</p>

                <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                        <div>
                            <p class="font-semibold">Dampak Reset Profile:</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                <li>Seluruh jawaban user dihapus permanen.</li>
                                <li>Progres dan Status Survey dihapus.</li>
                                <li>Activity, Group, dan Unit dikosongkan.</li>
                                <li>User harus melengkapi kembali Activity, Group, dan Unit sebelum mengisi survey.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <p class="mt-4 text-sm text-gray-500">Akun login, username, nama lengkap, email, nomor handphone, dan role tetap dipertahankan.</p>
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4">
                <button type="button" data-modal-close="resetProfileModal" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Batal</button>
                <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"><i class="fa-solid fa-user-rotate mr-2"></i>Ya, Reset Profile</button>
            </div>
        </form>
    </div>
</div>
