@php
    $questionBodyView = match ((int) $form->formtype_id) {
        1 => 'admin.units.question.partials.forms.general-questionnaire.question-body',
        2 => 'admin.units.question.partials.forms.customer-assessment-1-5.question-body',
        3 => 'admin.units.question.partials.forms.customer-assessment-1-7.question-body',
        4 => 'admin.units.question.partials.forms.engagement-assessment-1-5.question-body',
        5 => 'admin.units.question.partials.forms.engagement-assessment-1-7.question-body',
        6 => 'admin.units.question.partials.forms.ranking-1-3.question-body',
        7 => 'admin.units.question.partials.forms.ranking-1-5.question-body',
        8 => 'admin.units.question.partials.forms.strength-complaint-suggestion.question-body',
        9 => 'admin.units.question.partials.forms.complaint-suggestion.question-body',
        10 => 'admin.units.question.partials.forms.suggestion.question-body',
        11 => 'admin.units.question.partials.forms.competitor-1-5.question-body',
        13 => 'admin.units.question.partials.forms.competitor-1-7.question-body',
        default => null,
    };
@endphp

@if ($questionBodyView)
    @include($questionBodyView, [
        'form' => $form,
        'question' => $question,
        'competitors' => $competitors ?? collect(),
    ])
@else
    <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        Tampilan pertanyaan untuk tipe form #{{ $form->formtype_id }} belum tersedia.
    </div>
@endif
