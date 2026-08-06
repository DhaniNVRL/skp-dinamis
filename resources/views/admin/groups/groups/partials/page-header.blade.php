<div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <a
                href="{{ route('admin.activity') }}"
                class="mb-3 inline-flex items-center gap-2 text-sm font-medium
                    text-blue-600 transition hover:text-blue-700"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Activity
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Group & Complete Profile</h1>
            <p class="mt-1 text-sm text-gray-500">
                Pengaturan Group dan Complete Profile dari Activity
                <span class="font-semibold text-gray-800">{{ $activity->name }}</span>
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <div class="min-w-[140px] rounded-xl bg-indigo-50 px-5 py-4">
                <p class="text-xs font-medium uppercase tracking-wide text-indigo-500">Activity</p>
                <p class="mt-1 font-semibold text-indigo-700">{{ $activity->name }}</p>
            </div>
            <div class="min-w-[140px] rounded-xl bg-blue-50 px-5 py-4">
                <p class="text-xs font-medium uppercase tracking-wide text-blue-500">Jumlah Group</p>
                <p class="mt-1 text-xl font-bold text-blue-700">{{ $groups->count() }}</p>
            </div>
        </div>
    </div>
</div>
