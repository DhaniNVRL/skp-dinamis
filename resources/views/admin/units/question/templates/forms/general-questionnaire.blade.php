<template id="generalQuestionnaireTypeOptions">

    @foreach ($questionTypes as $questionType)
        <option
            value="{{ $questionType->id }}"
            data-description="{{ $questionType->description ?? '' }}"
        >
            {{ $questionType->name }}
        </option>
    @endforeach

</template>