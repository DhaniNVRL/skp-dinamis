<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="w-16 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">No</th>
                <th class="min-w-[180px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Responden</th>
                <th class="min-w-[160px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Activity</th>
                <th class="min-w-[160px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Group</th>
                <th class="min-w-[160px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Unit</th>
                <th class="w-40 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Status</th>
                <th class="w-32 px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600">Jawaban</th>
                <th class="w-28 px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse ($respondents as $profile)
                @php
                    $statusClass = match ($profile->monitoring_status) {
                        'completed'   => 'bg-emerald-100 text-emerald-700',
                        'in_progress' => 'bg-amber-100 text-amber-700',
                        default       => 'bg-slate-100 text-slate-600',
                    };
                    $statusIcon = match ($profile->monitoring_status) {
                        'completed'   => 'fa-circle-check',
                        'in_progress' => 'fa-spinner',
                        default       => 'fa-clock',
                    };
                @endphp
                <tr class="transition hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $respondents->firstItem() + $loop->index }}</td>
                    <td class="px-4 py-3">
                        <p class="text-sm font-semibold text-gray-800">{{ $profile->fullname ?: $profile->user?->username }}</p>
                        <p class="mt-0.5 text-xs text-gray-500">{{ $profile->user?->username }}</p>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $profile->activity?->name ?: '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $profile->group?->name ?: '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $profile->unit?->name ?: '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                            <i class="fa-solid {{ $statusIcon }}"></i>{{ $profile->monitoring_status_label }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex min-w-[40px] justify-center rounded-lg bg-blue-50 px-2.5 py-1 text-sm font-semibold text-blue-700">{{ $profile->user?->answers_count ?? 0 }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('monitoring.respondent.detail', ['userId' => $profile->user_id]) }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-3 py-2 text-xs font-medium text-white hover:bg-indigo-700">
                            <i class="fa-solid fa-eye"></i>Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-4 py-14 text-center text-sm text-gray-500"><i class="fa-regular fa-folder-open mb-2 block text-3xl text-gray-300"></i>Tidak ada responden sesuai filter.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
