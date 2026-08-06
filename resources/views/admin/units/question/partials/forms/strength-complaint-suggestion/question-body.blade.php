@php
    $questionTypeId = (int) (
        $question->questiontype_id
        ?? $question->id_questiontypes
        ?? 0
    );
@endphp

@switch($questionTypeId)

    {{-- Judul pertanyaan --}}
    @case(1)
        @include(
            'admin.units.question.partials.forms.strength-complaint-suggestion.options.title',
            [
                'question' => $question,
                'form' => $form,
            ]
        )
        @break

    {{-- Keunggulan, Keluhan, dan Saran --}}
    @case(2)
        @include(
            'admin.units.question.partials.forms.strength-complaint-suggestion.options.three-textareas',
            [
                'question' => $question,
                'form' => $form,
            ]
        )
        @break

    @default
        <div class="mb-5 rounded-lg border border-dashed border-red-300 bg-red-50 p-4">

            <p class="text-sm font-medium text-red-700">
                Tipe pertanyaan tidak ditemukan.
            </p>

            <p class="mt-1 text-xs text-red-600">
                ID tipe: {{ $questionTypeId ?: '-' }}
            </p>

        </div>

@endswitch