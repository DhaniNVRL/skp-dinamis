<div class="overflow-hidden rounded-xl border border-indigo-200 bg-indigo-50 shadow-sm">

    <div class="flex items-center justify-between gap-4 px-5 py-4">

        <div class="flex min-w-0 items-center gap-3">

            <span
                class="inline-flex min-w-10 shrink-0 items-center
                       justify-center rounded-lg bg-indigo-600
                       px-2.5 py-1 text-sm font-semibold text-white"
            >
                {{ $question->no_header ?: '-' }}
            </span>

            <div class="min-w-0">

                <h4 class="font-bold leading-6 text-gray-800">
                    {{ $question->name }}
                </h4>

                <p class="mt-1 text-xs text-indigo-600">
                    Judul Keluhan dan Saran
                </p>

            </div>

        </div>

        @include(
            'admin.units.question.partials.forms.question-action',
            [
                'question' => $question,
                'form' => $form,
            ]
        )

    </div>

</div>