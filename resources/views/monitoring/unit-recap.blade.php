@extends('layouts.app-role-modern')

@section('title', 'Grafik Rekap Unit')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-blue-600">Monitoring Survey</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-900">Grafik Rekap Semua Unit</h1>
                <p class="mt-1 text-sm text-slate-500">Progres pengisian responden pada setiap unit.</p>
            </div>
            <div class="rounded-xl bg-blue-50 px-5 py-3 text-sm font-semibold text-blue-800">
                {{ $activity->name }}
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('monitoring.unit-recap') }}" class="grid gap-4 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] md:items-end">
            <label class="block text-sm font-semibold text-slate-700" for="recapGroup">
                Group
                <select id="recapGroup" name="group_id" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">Semua Group</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}" {{ (string) ($recapFilters['group_id'] ?? '') === (string) $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm font-semibold text-slate-700" for="recapUnit">
                Unit
                <select id="recapUnit" name="unit_id" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">Semua Unit</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}" data-group-id="{{ $unit->group_id }}" {{ (string) ($recapFilters['unit_id'] ?? '') === (string) $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                    @endforeach
                </select>
            </label>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"><i class="fa-solid fa-filter"></i>Terapkan</button>
                @if (($recapFilters['group_id'] ?? null) || ($recapFilters['unit_id'] ?? null))
                    <a href="{{ route('monitoring.unit-recap') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50" title="Reset filter"><i class="fa-solid fa-rotate-left"></i></a>
                @endif
            </div>
        </form>
    </section>

    <section class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5">
            <p class="text-sm font-semibold text-blue-700">Total Responden</p>
            <p class="mt-2 text-3xl font-bold text-blue-950">{{ number_format($totalRespondents) }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5">
            <p class="text-sm font-semibold text-emerald-700">Sudah Mengisi</p>
            <p class="mt-2 text-3xl font-bold text-emerald-950">{{ number_format($totalCompleted) }}</p>
        </div>
        <div class="rounded-2xl border border-violet-100 bg-violet-50 p-5">
            <p class="text-sm font-semibold text-violet-700">Persentase Pengisian</p>
            <p class="mt-2 text-3xl font-bold text-violet-950">{{ number_format($completionPercentage, 2, ',', '.') }}%</p>
        </div>
    </section>

    @php
        $chartHeight = max(360, $unitRecaps->count() * 68);
    @endphp
    <div class="grid items-start gap-6 xl:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5">
                <h2 class="text-lg font-bold text-slate-900">Grafik Responden per Unit</h2>
            </div>
            <div style="height: {{ $chartHeight }}px"><canvas id="unitRecapChart"></canvas></div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-semibold text-slate-900">Tabel Persentase Pengisian per Unit</h2>
                </div>
                <a href="{{ route('monitoring.unit-recap.export', request()->only(['group_id', 'unit_id'])) }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-3.5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700"><i class="fa-solid fa-file-excel"></i>Export Excel</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">No.</th>
                        <th class="px-4 py-3">Unit</th>
                        <th class="px-4 py-3 text-right">Responden</th>
                        <th class="px-4 py-3 text-right">Mengisi</th>
                        <th class="px-4 py-3 text-right">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($unitRecaps as $unit)
                        @php
                            $percentageClass = match (true) {
                                $unit->completion_percentage <= 0 => 'bg-red-50 text-red-700',
                                $unit->completion_percentage >= 100 => 'bg-emerald-50 text-emerald-700',
                                default => 'bg-amber-50 text-amber-700',
                            };
                        @endphp
                        <tr>
                            <td class="px-4 py-4 text-slate-500">{{ $loop->iteration }}</td>
                            <td class="px-4 py-4"><p class="font-medium text-slate-900">{{ $unit->name }}</p><p class="mt-1 text-xs text-slate-500">{{ $unit->group_name }}</p></td>
                            <td class="px-4 py-4 text-right text-slate-700">{{ number_format($unit->respondent_count) }}</td>
                            <td class="px-4 py-4 text-right text-slate-700">{{ number_format($unit->completed_count) }}</td>
                            <td class="px-4 py-4 text-right">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $percentageClass }}">{{ number_format($unit->completion_percentage, 2, ',', '.') }}%</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-12 text-center text-slate-500">Belum ada unit pada activity ini.</td></tr>
                    @endforelse
                </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const group = document.getElementById('recapGroup');
    const unit = document.getElementById('recapUnit');

    if (group && unit) {
        const filterUnits = function () {
            const selectedGroup = group.value;
            Array.from(unit.options).forEach(function (option) {
                if (!option.value) return;
                const visible = !selectedGroup || option.dataset.groupId === selectedGroup;
                option.hidden = !visible;
                option.disabled = !visible;
            });

            const selected = unit.options[unit.selectedIndex];
            if (selected && selected.disabled) unit.value = '';
        };

        group.addEventListener('change', filterUnits);
        filterUnits();
    }

    const canvas = document.getElementById('unitRecapChart');
    if (!canvas || !window.Chart) return;

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: @json($unitRecaps->map(fn ($unit) => $unit->name)->values()),
            datasets: [
                { label: 'Total Responden (100%)', data: @json($unitRecaps->map(fn () => 100)->values()), backgroundColor: '#2563eb', borderRadius: 6 },
                { label: 'Sudah Mengisi', data: @json($unitRecaps->pluck('completion_percentage')->values()), backgroundColor: '#059669', borderRadius: 6 },
            ],
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100,
                    ticks: { callback: function (value) { return value + '%'; } },
                },
                y: { ticks: { autoSkip: false } },
            },
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function (context) { return context.dataset.label + ': ' + context.parsed.x + '%'; },
                    },
                },
            },
        },
    });
});
</script>
@endpush
