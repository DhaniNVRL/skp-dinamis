@php
    /*
    |--------------------------------------------------------------------------
    | KONFIGURASI FORM KOMPETITOR SKALA 1–5
    |--------------------------------------------------------------------------
    */

    $scaleValues = [1, 2, 3, 4, 5, 0];
@endphp

@include(
    'user.survey.forms.partials.competitor-fields',
    [
        'form' => $form,
        'questions' => $questions ?? $form->questions ?? collect(),
        'competitors' => $competitors ?? collect(),
        'answerMap' => $answerMap ?? [],
        'scaleValues' => $scaleValues,
    ]
)
