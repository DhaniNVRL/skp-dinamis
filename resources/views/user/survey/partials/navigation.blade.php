<div class="grid grid-cols-3 items-center gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-lg">
    <div class="justify-self-start">
        @if ($previousForm)
            <a href="{{ route('survey.show', $previousForm) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 font-medium text-gray-700 hover:bg-gray-50">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                <span>Form Sebelumnya</span>
            </a>
        @else
            <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 font-medium text-gray-700 hover:bg-gray-50">
                <i class="fa-solid fa-house" aria-hidden="true"></i>
                <span>Dashboard</span>
            </a>
        @endif
    </div>

    <div class="justify-self-center">
        @if ((int) $form->formtype_id === 12 && $isLastForm && $firstQuestionForm)
            <a href="{{ route('survey.show', $firstQuestionForm) }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 font-semibold text-white hover:bg-blue-700">
                <i class="fa-solid fa-clipboard-check" aria-hidden="true"></i>
                <span>Review Jawaban</span>
            </a>
        @endif
    </div>

    <div class="justify-self-end">
        @if ((int) $form->formtype_id === 12 && $isLastForm)
            <button type="submit" formaction="{{ route('survey.finish') }}" formmethod="POST" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-2.5 font-semibold text-white hover:bg-green-700">
                <i class="fa-solid fa-flag-checkered" aria-hidden="true"></i>
                <span>Akhiri Survei</span>
            </button>
        @else
            <button type="submit" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 font-semibold text-white hover:bg-indigo-700">
                <span>Simpan dan Lanjutkan</span>
                <i class="fa-solid fa-arrow-right shrink-0" aria-hidden="true"></i>
            </button>
        @endif
    </div>
</div>