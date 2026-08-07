@php
    $statusClass = match ($survey['status']) {
        'completed' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'in_progress' => 'border-amber-200 bg-amber-50 text-amber-700',
        default => 'border-slate-200 bg-slate-50 text-slate-600',
    };
    $statusIcon = match ($survey['status']) {
        'completed' => 'fa-circle-check',
        'in_progress' => 'fa-spinner',
        default => 'fa-clock',
    };
@endphp

<div class="mx-auto max-w-[1600px] space-y-6">
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-800">
            <i class="fa-solid fa-arrow-left"></i>{{ $backLabel }}
        </a>

        <div class="mt-4 flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $pageTitle }}</h1>
                <p class="mt-1 text-gray-500">{{ $pageDescription }}</p>
            </div>
            <div class="rounded-xl bg-blue-50 px-5 py-3 text-right">
                <p class="text-xs font-semibold uppercase text-blue-600">Jumlah Jawaban</p>
                <p class="text-2xl font-bold text-blue-700">{{ $answers->count() }}</p>
            </div>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm lg:col-span-2">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600"><i class="fa-solid fa-user"></i></div>
                <div><h2 class="font-semibold text-gray-900">Profil Responden</h2><p class="text-sm text-gray-500">Informasi akun dan lokasi responden.</p></div>
            </div>

            <dl class="grid gap-x-8 gap-y-5 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ([
                    'Username' => $user?->username,
                    'Nama Lengkap' => $profile?->fullname,
                    'Email' => $profile?->email,
                    'No. Handphone' => $profile?->no_handphone,
                    'Role' => $user?->role?->name,
                    'Activity' => $profile?->activity?->name,
                    'Group' => $profile?->group?->name,
                    'Unit' => $profile?->unit?->name,
                ] as $label => $value)
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ $label }}</dt>
                        <dd class="mt-1 break-words text-sm font-medium text-gray-800">{{ filled($value) ? $value : '-' }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        <div class="rounded-xl border p-5 shadow-sm {{ $statusClass }}">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/70"><i class="fa-solid {{ $statusIcon }}"></i></div>
                <div><p class="text-xs font-semibold uppercase tracking-wide opacity-70">Status Survey</p><p class="text-lg font-bold">{{ $survey['status_label'] }}</p></div>
            </div>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex items-center justify-between gap-4"><dt class="opacity-75">Mulai</dt><dd class="text-right font-semibold">{{ $survey['started_at'] ?: '-' }}</dd></div>
                <div class="flex items-center justify-between gap-4"><dt class="opacity-75">Selesai</dt><dd class="text-right font-semibold">{{ $survey['finished_at'] ?: '-' }}</dd></div>
                @if ($survey['reopened_at'] ?? null)
                    <div class="flex items-center justify-between gap-4"><dt class="opacity-75">Dibuka Kembali</dt><dd class="text-right font-semibold">{{ $survey['reopened_at'] }}</dd></div>
                @endif
                <div class="flex items-center justify-between gap-4 border-t border-current/10 pt-3"><dt class="opacity-75">Jumlah Jawaban</dt><dd class="font-bold">{{ $survey['answers_count'] }}</dd></div>
            </dl>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-5 py-4">
            <h2 class="text-lg font-semibold text-gray-900">Daftar Jawaban</h2>
            <p class="mt-1 text-sm text-gray-500">Seluruh jawaban ditampilkan tanpa pembatasan halaman.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">No</th>
                        <th class="min-w-[180px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Form</th>
                        <th class="min-w-[280px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Pertanyaan</th>
                        <th class="min-w-[190px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Objek Penilaian</th>
                        <th class="min-w-[300px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Jawaban</th>
                        <th class="min-w-[150px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Diperbarui</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($answers as $answer)
                        @php($questionNumber = trim(($answer->question?->no_header ?? '').($answer->question?->no ?? '')))
                        <tr class="align-top hover:bg-gray-50">
                            <td class="px-4 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-4 py-4 text-sm font-medium text-gray-800">{{ $answer->form?->name ?: '-' }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">
                                @if ($questionNumber)<span class="mr-2 inline-flex rounded bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700">{{ $questionNumber }}</span>@endif
                                {{ $answer->question?->name ?: 'Pertanyaan telah dihapus' }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600">
                                @if ($answer->subunit?->name)
                                    <div class="rounded-lg border border-purple-100 bg-purple-50 px-3 py-2"><p class="text-[11px] font-semibold uppercase tracking-wide text-purple-500">Sub Unit yang dinilai</p><p class="mt-1 font-medium text-purple-800">{{ $answer->subunit->name }}</p></div>
                                @elseif ($answer->competitor?->name)
                                    <div class="rounded-lg border border-amber-100 bg-amber-50 px-3 py-2"><p class="text-[11px] font-semibold uppercase tracking-wide text-amber-500">Kompetitor yang dinilai</p><p class="mt-1 font-medium text-amber-800">{{ $answer->competitor->name }}</p></div>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">Global</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="space-y-2">
                                    @foreach ($answer->review_details as $detail)
                                        <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2"><p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">{{ $detail['label'] }}</p><p class="mt-1 whitespace-pre-wrap break-words text-sm font-medium text-gray-900">{{ $detail['value'] }}</p></div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500">{{ $answer->updated_at?->format('d-m-Y H:i') ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500"><i class="fa-regular fa-folder-open mb-2 block text-2xl text-gray-400"></i>Responden belum memiliki jawaban.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
