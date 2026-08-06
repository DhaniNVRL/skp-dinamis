<div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Complete Profile</h1>
            <p class="mt-1 text-sm text-gray-500">Pertanyaan profil awal untuk Activity <span class="font-semibold text-gray-800">{{ $activity->name }}</span></p>
        </div>
        <div class="min-w-[150px] rounded-xl bg-blue-50 px-5 py-4"><p class="text-xs font-medium uppercase tracking-wide text-blue-500">Jumlah Pertanyaan</p><p class="mt-1 text-xl font-bold text-blue-700">{{ $cprofiles->count() }}</p></div>
    </div>
</div>
