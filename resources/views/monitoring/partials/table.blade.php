<section class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h2 class="font-semibold text-gray-900">Daftar Responden</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-5 py-3">No</th>
                    <th class="px-5 py-3">Username</th>
                    <th class="px-5 py-3">Nama</th>
                    <th class="px-5 py-3">Group</th>
                    <th class="px-5 py-3">Unit</th>
                    <th class="px-5 py-3">Status Survey</th>
                    @if ($isSurveyor)
                        <th class="px-5 py-3 text-center">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($respondents as $respondent)
                    @php
                        $statusClass = match ($respondent->monitoring_status) {
                            'completed' => 'bg-emerald-50 text-emerald-700',
                            'in_progress' => 'bg-amber-50 text-amber-700',
                            default => 'bg-gray-100 text-gray-700',
                        };
                    @endphp
                    <tr>
                        <td class="whitespace-nowrap px-5 py-4 text-gray-500">{{ $respondents->firstItem() + $loop->index }}</td>
                        <td class="whitespace-nowrap px-5 py-4 font-medium text-gray-900">{{ $respondent->user?->username ?? '-' }}</td>
                        <td class="px-5 py-4 text-gray-700">{{ $respondent->fullname ?: '-' }}</td>
                        <td class="px-5 py-4 text-gray-700">{{ $respondent->group?->name ?? '-' }}</td>
                        <td class="px-5 py-4 text-gray-700">{{ $respondent->unit?->name ?? '-' }}</td>
                        <td class="whitespace-nowrap px-5 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                {{ $respondent->monitoring_status_label }}
                            </span>
                        </td>
                        @if ($isSurveyor)
                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('surveyor.respondent.profile', $respondent->user_id) }}" class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-white px-3 py-2 text-xs font-semibold text-blue-700 transition hover:bg-gray-50" title="Lihat Profil">
                                        <i class="fa-solid fa-user"></i><span>Profil</span>
                                    </a>
                                    <a href="{{ route('surveyor.respondent.answers', $respondent->user_id) }}" class="inline-flex items-center gap-2 rounded-lg border border-indigo-200 bg-white px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-gray-50" title="Lihat Jawaban">
                                        <i class="fa-solid fa-clipboard-list"></i><span>Jawaban</span>
                                    </a>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isSurveyor ? 7 : 6 }}" class="px-5 py-12 text-center text-gray-500">Tidak ada responden yang sesuai filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($respondents->hasPages())
        <div class="border-t border-gray-200 px-5 py-4">{{ $respondents->links() }}</div>
    @endif
</section>
