<section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-blue-600">
                {{ auth()->user()?->hasRole('surveyor') ? 'Dashboard Surveyor' : 'Dashboard Client' }}
            </p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900">Monitoring Survey</h1>
        </div>

        <div class="rounded-xl bg-blue-50 px-5 py-3">
            <p class="mt-1 font-semibold text-blue-800">{{ $activity->name }}</p>
        </div>
    </div>
</section>
