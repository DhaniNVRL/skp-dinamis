<div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-lg font-semibold text-gray-800">
            Status Survei
        </h2>

        <span
            class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-semibold {{ $surveyStatus['class'] }}"
        >
            <i class="{{ $surveyStatus['icon'] }}"></i>
            {{ $surveyStatus['label'] }}
        </span>
    </div>

    <p class="text-sm leading-6 text-gray-500">
        {{ $surveyStatus['description'] }}
    </p>

    {{-- PROFILE WARNING --}}
    @if (!$isProfileComplete)
        <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation mt-0.5 text-amber-500"></i>

                <div>
                    <div class="text-sm font-semibold text-amber-700">
                        Profil Belum Lengkap
                    </div>

                    <p class="mt-1 text-xs leading-5 text-amber-600">
                        Lengkapi Bidang Kerja dan Unit/Jabatan sebelum memulai survei.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="mt-6 space-y-5 border-t border-gray-100 pt-5">
        <div>
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                Waktu Mulai
            </div>

            <div class="mt-1 text-sm font-semibold text-gray-700">
                {{ $surveySession?->started_at
                    ?->format('d M Y, H:i') ?? '-' }}
            </div>
        </div>

        <div>
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                Waktu Selesai
            </div>

            <div class="mt-1 text-sm font-semibold text-gray-700">
                {{ $surveySession?->finished_at
                    ?->format('d M Y, H:i') ?? '-' }}
            </div>
        </div>
    </div>

    {{-- SURVEY BUTTON --}}
    @if ($surveyStatus['key'] !== 'completed')
        @if ($isProfileComplete)
            <a
                href="{{ route('survey.index') }}"
                class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700"
            >
                <i class="fa-solid fa-clipboard-list"></i>

                @if ($surveyStatus['key'] === 'in_progress')
                    Lanjutkan Survei
                @else
                    Mulai Survei
                @endif
            </a>
        @else
            <button
                type="button"
                disabled
                title="Lengkapi profil terlebih dahulu"
                class="mt-6 inline-flex w-full cursor-not-allowed items-center justify-center gap-2 rounded-lg bg-gray-300 px-4 py-2.5 text-sm font-medium text-gray-500 opacity-70"
            >
                <i class="fa-solid fa-lock"></i>
                Mulai Survei
            </button>

            <a
                href="{{ route('profile.edit') }}"
                class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-medium text-blue-600 transition hover:bg-blue-100"
            >
                <i class="fa-solid fa-pen-to-square"></i>
                Lengkapi Profil
            </a>
        @endif
    @else
        <div class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-green-100 px-4 py-2.5 text-sm font-medium text-green-700">
            <i class="fa-solid fa-circle-check"></i>
            Survei Selesai
        </div>
    @endif
</div>