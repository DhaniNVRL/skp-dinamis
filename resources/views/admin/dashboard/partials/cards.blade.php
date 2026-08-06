<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <a href="{{ request()->fullUrlWithQuery(['status' => null, 'page' => null]) }}" class="rounded-xl border border-blue-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
        <div class="flex items-start justify-between">
            <div><p class="text-sm font-medium text-gray-500">Seluruh Role User</p><p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalRespondents) }}</p><p class="mt-1 text-xs text-gray-400">Total responden</p></div>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-600"><i class="fa-solid fa-users text-lg"></i></div>
        </div>
    </a>

    <a href="{{ request()->fullUrlWithQuery(['status' => 'completed', 'page' => null]) }}" class="rounded-xl border border-emerald-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
        <div class="flex items-start justify-between">
            <div><p class="text-sm font-medium text-gray-500">Sudah Mengisi</p><p class="mt-2 text-3xl font-bold text-emerald-600">{{ number_format($completedCount) }}</p><p class="mt-1 text-xs text-gray-400">Survey selesai</p></div>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600"><i class="fa-solid fa-circle-check text-lg"></i></div>
        </div>
    </a>

    <a href="{{ request()->fullUrlWithQuery(['status' => 'in_progress', 'page' => null]) }}" class="rounded-xl border border-amber-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
        <div class="flex items-start justify-between">
            <div><p class="text-sm font-medium text-gray-500">Sedang Mengisi</p><p class="mt-2 text-3xl font-bold text-amber-600">{{ number_format($inProgressCount) }}</p><p class="mt-1 text-xs text-gray-400">Survey berjalan</p></div>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-amber-600"><i class="fa-solid fa-spinner text-lg"></i></div>
        </div>
    </a>

    <a href="{{ request()->fullUrlWithQuery(['status' => 'not_started', 'page' => null]) }}" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
        <div class="flex items-start justify-between">
            <div><p class="text-sm font-medium text-gray-500">Belum Mengisi</p><p class="mt-2 text-3xl font-bold text-slate-600">{{ number_format($notStartedCount) }}</p><p class="mt-1 text-xs text-gray-400">Belum memulai</p></div>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-600"><i class="fa-solid fa-clock text-lg"></i></div>
        </div>
    </a>
</div>
