@php
    $questionNumber = trim(
        (string) ($question->no_header ?? '')
        . (string) ($question->no ?? '')
    );

    $questionTypeId = (int) (
        $question->questiontype_id
        ?? $question->id_questiontypes
        ?? 0
    );
    $formTypeId = isset($form)
        ? (int) ($form->formtype_id ?? 0)
        : 0;
    $isTitle = $formTypeId === 1
        ? $questionTypeId === 10
        : $questionTypeId === 1;
@endphp

@if (! $isTitle && $questionNumber !== '')
    <span class="inline-flex min-w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-100 px-2 py-1 text-xs font-bold text-indigo-700">
        {{ $questionNumber }}
    </span>
@endif
