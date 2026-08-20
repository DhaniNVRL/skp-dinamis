@switch((int) $formTypeId)
    {{-- GENERAL QUESTIONNAIRE --}}
    @case(1)
        @include(
            'admin.subunit.show-question.forms.general-questionnaire',
            [
                'form' => $form,
                'questions' => $questions,
                'subunits' => $subunits,
                'subunitIds' => $subunitIds,
                'activeMapSubUnit' => $activeMapSubUnit,
            ]
        )
        @break

    {{-- CUSTOMER ASSESSMENT 1-5 --}}
    @case(2)
        @include(
            'admin.subunit.show-question.forms.customer-assessment-1-5',
            [
                'form' => $form,
                'questions' => $questions,
                'subunits' => $subunits,
                'subunitIds' => $subunitIds,
                'activeMapSubUnit' => $activeMapSubUnit,
            ]
        )
        @break

    {{-- CUSTOMER ASSESSMENT 1-7 --}}
    @case(3)
        @include(
            'admin.subunit.show-question.forms.customer-assessment-1-7',
            [
                'form' => $form,
                'questions' => $questions,
                'subunits' => $subunits,
                'subunitIds' => $subunitIds,
                'activeMapSubUnit' => $activeMapSubUnit,
            ]
        )
        @break

    {{-- ENGAGEMENT ASSESSMENT 1-5 --}}
    @case(4)
        @include(
            'admin.subunit.show-question.forms.engagement-assessment-1-5',
            [
                'form' => $form,
                'questions' => $questions,
                'subunits' => $subunits,
                'subunitIds' => $subunitIds,
                'activeMapSubUnit' => $activeMapSubUnit,
            ]
        )
        @break

    {{-- ENGAGEMENT ASSESSMENT 1-7 --}}
    @case(5)
        @include(
            'admin.subunit.show-question.forms.engagement-assessment-1-7',
            [
                'form' => $form,
                'questions' => $questions,
                'subunits' => $subunits,
                'subunitIds' => $subunitIds,
                'activeMapSubUnit' => $activeMapSubUnit,
            ]
        )
        @break

    {{-- RANKING 1-3 --}}
    @case(6)
        @include(
            'admin.subunit.show-question.forms.ranking-1-3',
            [
                'form' => $form,
                'questions' => $questions,
            ]
        )
        @break

    {{-- RANKING 1-5 --}}
    @case(7)
        @include(
            'admin.subunit.show-question.forms.ranking-1-5',
            [
                'form' => $form,
                'questions' => $questions,
            ]
        )
        @break

    {{-- STRENGTHS, COMPLAINTS, SUGGESTIONS --}}
    @case(8)
        @include(
            'admin.subunit.show-question.forms.strengths-complaints-suggestions',
            [
                'form' => $form,
                'questions' => $questions,
            ]
        )
        @break

    {{-- COMPLAINTS AND SUGGESTIONS 1-5 --}}
    @case(9)
        @include(
            'admin.subunit.show-question.forms.complaints-suggestions-1-5',
            [
                'form' => $form,
                'questions' => $questions,
            ]
        )
        @break

    {{-- SUGGESTIONS --}}
    @case(10)
        @include(
            'admin.subunit.show-question.forms.suggestions',
            [
                'form' => $form,
                'questions' => $questions,
            ]
        )
        @break

    {{-- COMPETITOR ASSESSMENT 1-5 --}}
    @case(11)
        @include(
            'admin.subunit.show-question.forms.partials.competitor-assessment',
            [
                'form' => $form,
                'questions' => $questions,
                'competitors' => $competitors,
                'maximum' => 5,
            ]
        )
        @break

    {{-- DESCRIPTION --}}
    @case(12)
        @include(
            'admin.subunit.show-question.forms.description',
            [
                'form' => $form,
                'questions' => $questions,
            ]
        )
        @break

    {{-- COMPETITOR ASSESSMENT 1-7 --}}
    @case(13)
        @include(
            'admin.subunit.show-question.forms.partials.competitor-assessment',
            [
                'form' => $form,
                'questions' => $questions,
                'competitors' => $competitors,
                'maximum' => 7,
            ]
        )
        @break

    {{-- RESPONDENT-DEFINED COMPETITOR ASSESSMENT 1-7 --}}
    @case(14)
        @include(
            'admin.subunit.show-question.forms.respondent-competitor-assessment',
            [
                'form' => $form,
                'questions' => $questions,
            ]
        )
        @break

    @default
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-600">
            Form type {{ $formTypeId }} belum didukung.
        </div>
@endswitch
