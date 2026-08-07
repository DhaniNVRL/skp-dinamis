<div class="overflow-hidden rounded-xl border border-indigo-200 bg-indigo-50 shadow-sm">

    <div class="flex items-center justify-between gap-4 px-5 py-4">

        <div class="min-w-0">
            <h4 class="font-bold leading-6 text-gray-800">
                {{ $question->name }}
            </h4>
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
