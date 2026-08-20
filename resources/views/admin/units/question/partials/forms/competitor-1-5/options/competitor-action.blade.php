@php
    $questionTypeId = (int) (
        $question->questiontype_id
        ?? $question->id_questiontypes
        ?? 0
    );
@endphp

@switch($questionTypeId)

    @case(1)
        @include(
            'admin.units.question.partials.forms.competitor-1-5.options.title',
            [
                'question' => $question,
                'form' => $form,
            ]
        )
        @break

    @case(2)
        @include(
            'admin.units.question.partials.forms.competitor-1-5.options.competitor-assessment',
            [
                'question' => $question,
                'form' => $form,
                'competitors' => $competitors,
            ]
        )
        @break

    @default
        <div class="rounded-lg border border-dashed border-red-300 bg-red-50 p-4">
            <p class="text-sm font-medium text-red-700">
                Tipe pertanyaan kompetitor tidak ditemukan.
            </p>

            <p class="mt-1 text-xs text-red-600">
                ID tipe: {{ $questionTypeId ?: '-' }}
            </p>
        </div>

@endswitch
