<div class="space-y-3">

    @forelse ($question->options as $option)

        @include(
            'admin.units.question.partials.forms.general-questionnaire.options.option-item',
            [
                'question' => $question,
                'option' => $option,
                'inputType' => $inputType
            ]
        )

    @empty

        <div
            class="rounded-xl border border-dashed border-gray-300
                   bg-gray-50 px-5 py-8 text-center"
        >
            <div
                class="mx-auto flex h-11 w-11 items-center justify-center
                       rounded-full bg-white text-gray-400 shadow-sm"
            >
                @if ($inputType === 'radio')
                    <i class="fa-regular fa-circle-dot"></i>
                @else
                    <i class="fa-regular fa-square-check"></i>
                @endif
            </div>

            <p class="mt-3 text-sm font-medium text-gray-600">
                Belum ada pilihan jawaban
            </p>

            <p class="mt-1 text-xs text-gray-400">
                Tambahkan option untuk pertanyaan ini.
            </p>
        </div>

    @endforelse


    <button
        type="button"
        data-modal-open="createOptionModal"
        data-question-id="{{ $question->id }}"
        data-question-name="{{ $question->name }}"
        data-action="{{ route('options.store') }}"
        class="inline-flex items-center gap-2 rounded-lg
            border border-dashed border-indigo-300
            bg-indigo-50 px-3.5 py-2
            text-sm font-medium text-indigo-700
            transition hover:border-indigo-400
            hover:bg-indigo-100"
    >
        <i class="fa-solid fa-plus text-xs"></i>

        Tambah Option
    </button>

</div>