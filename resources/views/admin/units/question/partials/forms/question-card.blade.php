<div class="overflow-hidden rounded-xl border border-gray-200 bg-white">

    <div class="px-5 py-4">

        @switch((int) $form->formtype_id)

            {{-- General Questionnaire --}}
            @case(1)
                @include(
                    'admin.units.question.partials.forms.general-questionnaire.question-body',
                    [
                        'question' => $question,
                        'form' => $form,
                    ]
                )
                @break

            {{-- Customer Assessment 1–5 --}}
            @case(2)
                @include(
                    'admin.units.question.partials.forms.customer-assessment-1-5.question-body',
                    [
                        'question' => $question,
                        'form' => $form,
                    ]
                )
                @break

            {{-- Customer Assessment 1–7 --}}
            @case(3)
                @include(
                    'admin.units.question.partials.forms.customer-assessment-1-7.question-body',
                    [
                        'question' => $question,
                        'form' => $form,
                    ]
                )
                @break

            {{-- Engagement Assessment 1–5 --}}
            @case(4)
                @include(
                    'admin.units.question.partials.forms.engagement-assessment-1-5.question-body',
                    [
                        'question' => $question,
                        'form' => $form,
                    ]
                )
                @break

            {{-- Engagement Assessment 1–7 --}}
            @case(5)
                @include(
                    'admin.units.question.partials.forms.engagement-assessment-1-7.question-body',
                    [
                        'question' => $question,
                        'form' => $form,
                    ]
                )
                @break

            {{-- Ranking 1–3 --}}
            @case(6)
                @include(
                    'admin.units.question.partials.forms.ranking-1-3.question-body',
                    [
                        'question' => $question,
                        'form' => $form,
                    ]
                )
                @break

            {{-- Ranking 1–5 --}}
            @case(7)
                @include(
                    'admin.units.question.partials.forms.ranking-1-5.question-body',
                    [
                        'question' => $question,
                        'form' => $form,
                    ]
                )
                @break

            {{-- Strength, Complaint, Suggestion --}}
            @case(8)
                @include(
                    'admin.units.question.partials.forms.strength-complaint-suggestion.question-body',
                    [
                        'question' => $question,
                        'form' => $form,
                    ]
                )
                @break

            {{-- Complaint & Suggestion --}}
            @case(9)
                @include(
                    'admin.units.question.partials.forms.complaint-suggestion.question-body',
                    [
                        'question' => $question,
                        'form' => $form,
                    ]
                )
                @break

            {{-- Suggestion --}}
            @case(10)
                @include(
                    'admin.units.question.partials.forms.suggestion.question-body',
                    [
                        'question' => $question,
                        'form' => $form,
                    ]
                )
                @break

            {{-- Competitor --}}
            @case(11)
                @include(
                    'admin.units.question.partials.forms.competitor-1-7.question-body',
                    [
                        'question' => $question,
                        'form' => $form,
                    ]
                )
                @break

            {{-- Description --}}
            @case(12)
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <p class="text-sm italic text-gray-500">
                        Form description tidak memiliki pertanyaan.
                    </p>
                </div>
                @break

            @default
                <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                    <p class="text-sm text-red-600">
                        Tampilan untuk tipe form ini belum tersedia.
                    </p>
                </div>

        @endswitch

    </div>

</div>