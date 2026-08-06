<div class="sticky bottom-0 z-20 flex items-center justify-between gap-3 rounded-xl border border-gray-200 bg-white/95 p-4 shadow-lg backdrop-blur">
    @if ($previousForm)
        <a href="{{ route('survey.show', $previousForm) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 font-medium text-gray-700 hover:bg-gray-50">
            <i class="fa-solid fa-arrow-left"></i> Form Sebelumnya
        </a>
    @else
        <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 font-medium text-gray-700 hover:bg-gray-50">
            <i class="fa-solid fa-house"></i> Dashboard
        </a>
    @endif

    @if ((int) $form->formtype_id === 12 && $isLastForm)
        <button type="submit" formaction="{{ route('survey.finish') }}" formmethod="POST" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-2.5 font-semibold text-white hover:bg-green-700">
            <i class="fa-solid fa-flag-checkered"></i> Akhiri Survei
        </button>
    @else
        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 font-semibold text-white hover:bg-indigo-700">
            Simpan dan Lanjutkan <i class="fa-solid fa-arrow-right"></i>
        </button>
    @endif
</div>
