<template id="generalQuestionnaireTypeOptions">

    @php
        $titleOnlyType = $questionTypes->first(
            fn ($questionType) => $questionType->isTitleOnly()
        );
    @endphp

    @foreach ($questionTypes->reject(fn ($questionType) => $questionType->isTitleOnly()) as $questionType)
        <option
            value="{{ $questionType->id }}"
            data-description="{{ $questionType->description ?? '' }}"
        >
            {{ $questionType->name }}
        </option>
    @endforeach

    @if ($titleOnlyType)
        <option
            value="{{ $titleOnlyType->id }}"
            data-description="{{ $titleOnlyType->description }}"
            data-title-only="1"
        >
            {{ $titleOnlyType->name }}
        </option>
    @endif

</template>
