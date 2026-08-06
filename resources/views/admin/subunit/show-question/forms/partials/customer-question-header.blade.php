<div class="mb-5">
    <div class="flex items-start gap-3">
        @include(
            'admin.subunit.show-question.forms.partials.question-number',
            [
                'question' => $question,
            ]
        )

        <div class="min-w-0">
            <h3 class="font-semibold text-gray-800">
                {{ $question->name }}
            </h3>

            @if (!empty($typeLabel))
                <p class="mt-1 text-xs text-gray-500">
                    {{ $typeLabel }}
                </p>
            @endif
        </div>
    </div>
</div>