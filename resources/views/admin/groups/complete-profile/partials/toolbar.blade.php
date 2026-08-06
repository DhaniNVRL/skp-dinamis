<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div><h2 class="text-xl font-semibold text-gray-800">Complete Profile</h2><p class="text-sm text-gray-500">Pertanyaan Group dan Unit untuk pertama kali login.</p></div>
        @if ($cprofiles->count() === 0)
            <button type="button" data-modal-open="createCprofileModal" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-green-700"><i class="fa-solid fa-plus"></i>Tambah Pertanyaan</button>
        @endif
    </div>
</div>
