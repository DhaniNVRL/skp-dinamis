<div class="space-y-3">

    <div class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-4">
        <!-- Nomor -->
        <span
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-sm font-semibold text-blue-700"
        >
            {{ $question->no_header }}{{ $question->no }}
        </span>

        <!-- Judul -->
        <div class="min-w-0 flex-1">
            <h4 class="truncate text-sm font-semibold text-gray-800">
                {{ $question->name }}
            </h4>
        </div>

        <!-- Action -->
        <div class="flex shrink-0 items-center gap-2">
            @include(
                'admin.units.question.partials.forms.question-action',
                [
                    'question' => $question,
                    'form' => $form,
                ]
            )
        </div>
    </div>

    <div class="flex items-center justify-between gap-4">

        <div>
            <p class="text-sm font-medium text-gray-700">
                Pilihan tunggal
            </p>

            <p class="mt-0.5 text-xs text-gray-500">
                Responden hanya dapat memilih satu jawaban.
            </p>
        </div>

        <span
            class="inline-flex shrink-0 items-center gap-1.5
                   rounded-full bg-blue-50 px-3 py-1
                   text-xs font-medium text-blue-700"
        >
            <i class="fa-regular fa-circle-dot"></i>
            Satu pilihan
        </span>

    </div>

    @include(
        'admin.units.question.partials.forms.general-questionnaire.options.option-list',
        [
            'question' => $question,
            'inputType' => 'radio'
        ]
    )

</div>