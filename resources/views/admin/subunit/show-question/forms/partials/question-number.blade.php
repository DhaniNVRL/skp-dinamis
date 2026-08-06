@php
    $questionNumber = trim(
        (string) ($question->no_header ?? '')
        . (string) ($question->no ?? '')
    );
@endphp

@if ($questionNumber !== '')
    <span class="inline-flex min-w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-100 px-2 py-1 text-xs font-bold text-indigo-700">
        {{ $questionNumber }}
    </span>
@endif