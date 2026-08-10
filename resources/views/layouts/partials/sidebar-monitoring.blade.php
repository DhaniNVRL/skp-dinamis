<aside class="fixed bottom-12 left-0 top-16 z-40 w-64 border-r border-slate-700 bg-slate-800 text-white">
    <div class="border-b border-slate-700 px-5 py-5">
        <p class="mt-1 truncate text-sm font-medium text-white">{{ auth()->user()->profile?->activity?->name ?? 'Activity belum ditentukan' }}</p>
    </div>

    <nav class="p-3">
        <a href="{{ route('monitoring.dashboard') }}" class="flex items-center gap-3 rounded-lg bg-blue-600 px-4 py-3 text-sm font-semibold text-white">
            <i class="fa-solid fa-chart-line w-5 text-center"></i>
            <span>Dashboard Monitoring</span>
        </a>
    </nav>
</aside>
