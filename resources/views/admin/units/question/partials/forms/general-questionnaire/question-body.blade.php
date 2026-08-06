@switch((int) $question->questiontype_id)

    @case(1)

        @include(
            'admin.units.question.partials.forms.general-questionnaire.options.text',
            ['question' => $question]
        )

        @break


    @case(2)

        @include(
            'admin.units.question.partials.forms.general-questionnaire.options.textarea',
            ['question' => $question]
        )

        @break


    @case(3)

        @include(
            'admin.units.question.partials.forms.general-questionnaire.options.radio',
            ['question' => $question]
        )

        @break


    @case(4)

        @include(
            'admin.units.question.partials.forms.general-questionnaire.options.checkbox',
            ['question' => $question]
        )

        @break

    @case(5)

        @include(
            'admin.units.question.partials.forms.general-questionnaire.options.dropdown',
            [
                'question' => $question
            ]
        )

        @break

    @case(6)

        @include(
            'admin.units.question.partials.forms.general-questionnaire.options.number',
            ['question' => $question]
        )

        @break
    
    @case(7)

        @include(
            'admin.units.question.partials.forms.general-questionnaire.options.date',
            [
                'question' => $question
            ]
        )

        @break


    @case(8)

        @include(
            'admin.units.question.partials.forms.general-questionnaire.options.email',
            [
                'question' => $question
            ]
        )

        @break


    @case(9)

        @include(
            'admin.units.question.partials.forms.general-questionnaire.options.phone',
            ['question' => $question]
        )

        @break


    @default

        <div
            class="rounded-xl border border-amber-200
                   bg-amber-50 px-4 py-3"
        >
            <div class="flex items-center gap-2 text-sm text-amber-700">

                <i class="fa-solid fa-triangle-exclamation"></i>

                <span>
                    Tampilan untuk tipe pertanyaan
                    <strong>#{{ $question->questiontype_id }}</strong>
                    belum tersedia.
                </span>

            </div>
        </div>

@endswitch