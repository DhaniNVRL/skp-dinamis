<div class="mb-6 overflow-hidden rounded-xl border border-blue-200 bg-blue-50 shadow-sm">
    <div class="flex items-center justify-between gap-4 px-5 py-4">

        <div class="flex-1">
            <h4 class="text-center text-lg font-bold text-gray-800">
                {{ $question->name }}
            </h4>
        </div>

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
</div>