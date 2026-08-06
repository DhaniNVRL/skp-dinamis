@php
    $groupId = $units->group_id
        ?? $units->group_id;
@endphp

<div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
        <div class="relative z-10">
            <a
                href="{{ route('admin.units', [
                    'id' => $groupId,
                    'tab' => 'unit',
                ]) }}"
                class="mb-3 inline-flex items-center gap-2 text-sm font-medium text-blue-600 transition hover:text-blue-800"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Unit
            </a>

            <h1 class="text-2xl font-bold text-gray-800">
                Sub Unit
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Pengaturan Sub Unit dari Unit

                <span class="font-semibold text-gray-700">
                    {{ $units->name }}
                </span>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @if ($units->group)
                <div class="rounded-lg bg-indigo-50 px-4 py-3">
                    <div class="text-xs font-semibold uppercase tracking-wide text-indigo-500">
                        Group
                    </div>

                    <div class="mt-1 font-semibold text-indigo-700">
                        {{ $units->group->name }}
                    </div>
                </div>
            @endif

            <div class="rounded-lg bg-blue-50 px-4 py-3">
                <div class="text-xs font-semibold uppercase tracking-wide text-blue-500">
                    Jumlah Sub Unit
                </div>

                <div class="mt-1 text-xl font-bold text-blue-700">
                    {{ $subunits->total() }}
                </div>
            </div>
        </div>
    </div>
</div>