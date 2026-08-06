<div id="respondentDetailModal" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="max-h-[92vh] w-full max-w-6xl overflow-hidden rounded-xl bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Detail Responden dan Jawaban</h3>
                <p class="mt-1 text-sm text-gray-500">Informasi profil serta seluruh jawaban survey.</p>
            </div>
            <button type="button" data-modal-close="respondentDetailModal" class="text-gray-400 hover:text-red-600"><i class="fa-solid fa-xmark text-xl"></i></button>
        </div>

        <div id="respondentDetailLoading" class="flex min-h-[300px] items-center justify-center p-8 text-gray-500">
            <i class="fa-solid fa-spinner fa-spin mr-3"></i>Memuat data responden...
        </div>

        <div id="respondentDetailError" class="hidden p-8 text-center text-sm text-red-600"></div>

        <div id="respondentDetailContent" class="hidden max-h-[calc(92vh-82px)] overflow-y-auto p-6">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 lg:col-span-2">
                    <h4 class="mb-4 font-semibold text-gray-900">User Profile</h4>
                    <dl id="respondentProfileGrid" class="grid grid-cols-1 gap-x-6 gap-y-4 text-sm sm:grid-cols-2"></dl>
                </div>
                <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-4">
                    <h4 class="mb-4 font-semibold text-indigo-900">Status Survey</h4>
                    <div id="respondentSurveySummary" class="space-y-3 text-sm"></div>
                </div>
            </div>

            <div class="mt-6 overflow-hidden rounded-xl border border-gray-200">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3"><h4 class="font-semibold text-gray-900">Daftar Jawaban</h4></div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100"><tr><th class="w-16 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">No</th><th class="min-w-[180px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Form</th><th class="min-w-[260px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Pertanyaan</th><th class="min-w-[260px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Jawaban</th></tr></thead>
                        <tbody id="respondentAnswerBody" class="divide-y divide-gray-100 bg-white"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
