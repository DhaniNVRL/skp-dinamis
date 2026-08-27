@php
    /*
    |--------------------------------------------------------------------------
    | Meaningful Answer Validation
    |--------------------------------------------------------------------------
    |
    | Validasi khusus jawaban bermakna hanya berlaku untuk role_id 2 dan 4.
    |
    | Pada Kuesioner Umum aturan ini HANYA berlaku untuk CHILD ANSWER.
    |
    | Tidak berlaku untuk:
    | - Type 1 Short Text
    | - Type 2 Textarea biasa
    | - Number
    | - Date
    | - Email
    |
    */
    $useMeaningfulValidation = in_array(
        (int) auth()->user()?->role_id,
        [2, 4],
        true
    );

    /*
    |--------------------------------------------------------------------------
    | Urutkan pertanyaan secara natural
    |--------------------------------------------------------------------------
    |
    | Contoh hasil:
    |
    | A1
    | A2
    | A9
    | A10
    |
    | E0
    | E3.1
    | E3.2
    | E3.9
    | E3.10
    | E3.11
    |
    | Nomor kembar tetap diperbolehkan.
    |
    */
    $sortedQuestions = $questions
        ->sort(function ($a, $b) {

            /*
            |--------------------------------------------------------------------------
            | 1. Sort no_header
            |--------------------------------------------------------------------------
            */
            $headerA = trim(
                (string) ($a->no_header ?? '')
            );

            $headerB = trim(
                (string) ($b->no_header ?? '')
            );

            $headerCompare = strnatcasecmp(
                $headerA,
                $headerB
            );

            if ($headerCompare !== 0) {
                return $headerCompare;
            }

            /*
            |--------------------------------------------------------------------------
            | 2. Sort no
            |--------------------------------------------------------------------------
            */
            $noA = trim(
                (string) ($a->no ?? '0')
            );

            $noB = trim(
                (string) ($b->no ?? '0')
            );

            $noCompare = strnatcasecmp(
                $noA,
                $noB
            );

            if ($noCompare !== 0) {
                return $noCompare;
            }

            /*
            |--------------------------------------------------------------------------
            | 3. Jika nomor sama, gunakan ID
            |--------------------------------------------------------------------------
            */
            return (int) $a->id <=> (int) $b->id;
        })
        ->values();

    /*
    |--------------------------------------------------------------------------
    | Conditional Branch
    |--------------------------------------------------------------------------
    */
    $conditionalByQuestion = collect(
        $conditionalBranches ?? []
    )
        ->flatMap(function (array $branch) {

            $trigger = [
                'parent_id' =>
                    (int) $branch['parent_id'],

                'option_ids' =>
                    array_map(
                        'intval',
                        $branch['affirmative_option_ids']
                    ),
            ];

            $shown = collect(
                $branch['dependent_question_ids'] ?? []
            )->map(
                fn ($questionId) => [
                    'question_id' =>
                        (int) $questionId,

                    'mode' =>
                        'show',

                    'trigger' =>
                        $trigger,
                ]
            );

            $skipped = collect(
                $branch['skipped_question_ids'] ?? []
            )->map(
                fn ($questionId) => [
                    'question_id' =>
                        (int) $questionId,

                    'mode' =>
                        'hide',

                    'trigger' =>
                        $trigger,
                ]
            );

            return $shown->concat(
                $skipped
            );
        })
        ->groupBy(
            'question_id'
        )
        ->map(
            fn ($rules) => [
                'show_rules' =>
                    $rules
                        ->where('mode', 'show')
                        ->pluck('trigger')
                        ->values()
                        ->all(),

                'hide_rules' =>
                    $rules
                        ->where('mode', 'hide')
                        ->pluck('trigger')
                        ->values()
                        ->all(),
            ]
        );
@endphp


<div class="space-y-5">

    @forelse ($sortedQuestions as $question)

        @php
            /*
            |--------------------------------------------------------------------------
            | Question Type
            |--------------------------------------------------------------------------
            */
            $questionTypeId =
                (int) $question->questiontype_id;

            $isTitleOnly =
                $question->questiontype?->isTitleOnly()
                ?? false;

            /*
            |--------------------------------------------------------------------------
            | Stored Answer
            |--------------------------------------------------------------------------
            */
            $storedAnswer = data_get(
                $answerMap,
                "{$question->id}.0.0",
                []
            );

            /*
            |--------------------------------------------------------------------------
            | Stored Value
            |--------------------------------------------------------------------------
            */
            $storedValue = old(
                "answers.{$question->id}.value",
                data_get(
                    $storedAnswer,
                    'value'
                )
            );

            /*
            |--------------------------------------------------------------------------
            | Stored Child
            |--------------------------------------------------------------------------
            */
            $storedChildren = old(
                "answers.{$question->id}.child",
                data_get(
                    $storedAnswer,
                    'child',
                    []
                )
            );

            /*
            |--------------------------------------------------------------------------
            | Nomor pertanyaan
            |--------------------------------------------------------------------------
            */
            $questionNumber = trim(
                (string) ($question->no_header ?? '')
                .
                (string) ($question->no ?? '')
            );

            /*
            |--------------------------------------------------------------------------
            | Conditional Rule
            |--------------------------------------------------------------------------
            */
            $conditionalRule =
                $conditionalByQuestion->get(
                    (int) $question->id
                );

            $matchesConditionalTrigger =
                function (array $trigger) use ($answerMap): bool {

                    $selected = data_get(
                        $answerMap,
                        $trigger['parent_id'] . '.0.0.value'
                    );

                    return in_array(
                        (int) $selected,
                        array_map(
                            'intval',
                            $trigger['option_ids']
                        ),
                        true
                    );
                };

            $showRules = collect(
                $conditionalRule['show_rules'] ?? []
            );

            $hideRules = collect(
                $conditionalRule['hide_rules'] ?? []
            );

            $conditionalVisible =
                ! $conditionalRule
                ||
                (
                    (
                        $showRules->isEmpty()
                        ||
                        $showRules->contains(
                            $matchesConditionalTrigger
                        )
                    )
                    &&
                    ! $hideRules->contains(
                        $matchesConditionalTrigger
                    )
                );
        @endphp


        {{-- ================================================================ --}}
        {{-- TITLE ONLY --}}
        {{-- ================================================================ --}}

        @if ($isTitleOnly)

            <section
                data-question-container
                data-question-title
                data-question-id="{{ $question->id }}"

                @if ($conditionalRule)
                    data-conditional-question
                    data-conditional-rules='@json($conditionalRule)'

                    @unless ($conditionalVisible)
                        hidden
                    @endunless
                @endif

                class="overflow-hidden rounded-xl
                       border border-blue-200
                       bg-gradient-to-r
                       from-blue-50 to-indigo-50
                       shadow-sm"
            >
                <div
                    class="border-l-4
                           border-blue-600
                           px-6 py-5"
                >
                    <h2
                        class="text-xl font-bold
                               leading-relaxed
                               text-gray-900"
                    >
                        {{ $question->name }}
                    </h2>
                </div>
            </section>

        @else

            <div
                data-question-container
                data-question-id="{{ $question->id }}"
                data-question-type="{{ $questionTypeId }}"

                @if ($conditionalRule)
                    data-conditional-question
                    data-conditional-rules='@json($conditionalRule)'

                    @unless ($conditionalVisible)
                        hidden
                    @endunless
                @endif

                class="rounded-xl border
                       border-gray-200
                       bg-white p-5
                       shadow-sm
                       {{ $conditionalVisible ? '' : 'hidden' }}"
            >

                {{-- ======================================================== --}}
                {{-- QUESTION HEADER --}}
                {{-- ======================================================== --}}

                <div class="mb-5 flex items-start gap-3">

                    <span
                        class="inline-flex h-9 min-w-9
                               shrink-0 items-center
                               justify-center rounded-lg
                               bg-indigo-100 px-2
                               text-sm font-semibold
                               text-indigo-700"
                    >
                        {{ $questionNumber }}
                    </span>


                    <div class="min-w-0 flex-1">

                        <label class="font-semibold text-gray-900">
                            {{ $question->name }}
                        </label>


                        @if ($questionTypeId === 1)

                            <p
                                class="mt-1 text-xs
                                       uppercase tracking-wide
                                       text-gray-400"
                            >
                                Jawaban singkat
                            </p>

                        @elseif ($questionTypeId === 2)

                            <p
                                class="mt-1 text-xs
                                       uppercase tracking-wide
                                       text-gray-400"
                            >
                                Jawaban panjang
                            </p>

                        @elseif ($questionTypeId === 3)

                            <p
                                class="mt-1 text-xs
                                       uppercase tracking-wide
                                       text-gray-400"
                            >
                                Pilih satu jawaban
                            </p>

                        @elseif ($questionTypeId === 4)

                            <p
                                class="mt-1 text-xs
                                       uppercase tracking-wide
                                       text-gray-400"
                            >
                                Pilih satu atau beberapa jawaban
                            </p>

                        @elseif (
                            in_array(
                                $questionTypeId,
                                [6, 9],
                                true
                            )
                        )

                            <p
                                class="mt-1 text-xs
                                       uppercase tracking-wide
                                       text-gray-400"
                            >
                                Jawaban angka
                            </p>

                        @elseif ($questionTypeId === 7)

                            <p
                                class="mt-1 text-xs
                                       uppercase tracking-wide
                                       text-gray-400"
                            >
                                Pilih tanggal
                            </p>

                        @elseif ($questionTypeId === 8)

                            <p
                                class="mt-1 text-xs
                                       uppercase tracking-wide
                                       text-gray-400"
                            >
                                Alamat email
                            </p>

                        @endif

                    </div>

                </div>


                {{-- ======================================================== --}}
                {{-- TYPE 1: SHORT TEXT --}}
                {{-- ======================================================== --}}

                @if ($questionTypeId === 1)

                    <div class="relative">

                        <div
                            class="pointer-events-none
                                   absolute inset-y-0 left-0
                                   flex items-center
                                   pl-4 text-gray-400"
                        >
                            <i class="fa-regular fa-pen-to-square"></i>
                        </div>


                        <input
                            id="answer-{{ $question->id }}"
                            type="text"

                            name="answers[{{ $question->id }}][value]"

                            value="{{ $storedValue }}"

                            required

                            maxlength="500"

                            autocomplete="off"

                            placeholder="Tulis jawaban singkat..."

                            class="w-full rounded-lg
                                   border border-gray-300
                                   py-3 pl-11 pr-4
                                   text-sm outline-none
                                   transition
                                   focus:border-indigo-500
                                   focus:ring-2
                                   focus:ring-indigo-100"
                        >

                    </div>


                {{-- ======================================================== --}}
                {{-- TYPE 2: TEXTAREA BIASA --}}
                {{-- ======================================================== --}}

                @elseif ($questionTypeId === 2)

                    <textarea
                        id="answer-{{ $question->id }}"

                        name="answers[{{ $question->id }}][value]"

                        rows="4"

                        required

                        maxlength="5000"

                        placeholder="Tulis jawaban..."

                        class="w-full rounded-lg
                               border border-gray-300
                               px-4 py-3
                               text-sm outline-none
                               transition
                               focus:border-indigo-500
                               focus:ring-2
                               focus:ring-indigo-100"
                    >{{ $storedValue }}</textarea>


                {{-- ======================================================== --}}
                {{-- TYPE 3: RADIO --}}
                {{-- ======================================================== --}}

                @elseif ($questionTypeId === 3)

                    <div
                        data-option-group
                        data-option-type="radio"
                        class="space-y-3"
                    >

                        @forelse (
                            $question->options
                                ->sortBy('no')
                                ->values()
                            as $option
                        )

                            @php
                                $hasChild =
                                    (int) $option->has_child === 1;

                                $isChecked =
                                    (string) $storedValue
                                    ===
                                    (string) $option->id;

                                $childValue = data_get(
                                    $storedChildren,
                                    $option->id,
                                    ''
                                );

                                $childErrorKey =
                                    "answers.{$question->id}.child.{$option->id}";

                                $hasChildError =
                                    $errors->has(
                                        $childErrorKey
                                    );
                            @endphp


                            <div
                                data-option-item
                                class="rounded-lg border
                                       border-gray-200
                                       bg-white transition
                                       hover:border-indigo-300
                                       hover:bg-indigo-50/40"
                            >

                                <label
                                    for="option-{{ $question->id }}-{{ $option->id }}"
                                    class="flex cursor-pointer
                                           items-start gap-3 p-4"
                                >

                                    <input
                                        id="option-{{ $question->id }}-{{ $option->id }}"

                                        type="radio"

                                        name="answers[{{ $question->id }}][value]"

                                        value="{{ $option->id }}"

                                        data-option-input

                                        data-has-child="{{ $hasChild ? '1' : '0' }}"

                                        data-child-target="child-{{ $question->id }}-{{ $option->id }}"

                                        @checked($isChecked)

                                        required

                                        class="mt-0.5 h-4 w-4
                                               border-gray-300
                                               text-indigo-600
                                               focus:ring-indigo-500"
                                    >


                                    <span
                                        class="text-sm font-medium
                                               text-gray-700"
                                    >
                                        {{ $option->answer_text }}
                                    </span>

                                </label>


                                @if ($hasChild)

                                    <div
                                        id="child-{{ $question->id }}-{{ $option->id }}"

                                        data-child-container

                                        class="{{ $isChecked ? '' : 'hidden' }}
                                               border-t border-gray-200
                                               px-4 py-4"
                                    >

                                        @if (
                                            filled(
                                                $option->answer_text2
                                            )
                                        )

                                            <label
                                                for="child-answer-{{ $question->id }}-{{ $option->id }}"

                                                class="mb-2 block
                                                       text-sm font-medium
                                                       text-gray-700"
                                            >
                                                {{ $option->answer_text2 }}
                                            </label>

                                        @endif


                                        <textarea
                                            id="child-answer-{{ $question->id }}-{{ $option->id }}"

                                            name="answers[{{ $question->id }}][child][{{ $option->id }}]"

                                            rows="3"

                                            data-child-input

                                            @if ($useMeaningfulValidation)
                                                data-meaningful-answer
                                                data-answer-type="child"
                                                data-answer-label="Jawaban tambahan"
                                            @endif

                                            @required($isChecked)

                                            @disabled(!$isChecked)

                                            maxlength="5000"

                                            placeholder="{{ $option->answer_text2 ?: 'Tulis jawaban tambahan...' }}"

                                            class="w-full rounded-lg
                                                   border
                                                   bg-white
                                                   px-4 py-3
                                                   text-sm outline-none
                                                   transition
                                                   focus:border-indigo-500
                                                   focus:ring-2
                                                   focus:ring-indigo-100
                                                   {{ $hasChildError
                                                        ? 'border-red-500 ring-2 ring-red-100'
                                                        : 'border-gray-300'
                                                   }}"
                                        >{{ $childValue }}</textarea>


                                        @error($childErrorKey)
                                            <p
                                                class="mt-2 text-sm
                                                       font-medium
                                                       text-red-600"
                                                data-meaningful-error
                                            >
                                                <i
                                                    class="fa-solid
                                                           fa-circle-exclamation
                                                           mr-1"
                                                ></i>

                                                {{ $message }}
                                            </p>
                                        @enderror

                                    </div>

                                @endif

                            </div>


                        @empty

                            <div
                                class="rounded-lg border
                                       border-dashed
                                       border-gray-300
                                       p-5 text-center
                                       text-sm text-gray-500"
                            >
                                Pilihan jawaban belum tersedia.
                            </div>

                        @endforelse

                    </div>


                {{-- ======================================================== --}}
                {{-- TYPE 4: CHECKBOX --}}
                {{-- ======================================================== --}}

                @elseif ($questionTypeId === 4)

                    @php
                        $checkedValues = is_array(
                            $storedValue
                        )
                            ? array_map(
                                'strval',
                                $storedValue
                            )
                            : [];
                    @endphp


                    <div
                        data-option-group
                        data-option-type="checkbox"
                        data-required-group
                        class="space-y-3"
                    >

                        @forelse (
                            $question->options
                                ->sortBy('no')
                                ->values()
                            as $option
                        )

                            @php
                                $hasChild =
                                    (int) $option->has_child === 1;

                                $isChecked = in_array(
                                    (string) $option->id,
                                    $checkedValues,
                                    true
                                );

                                $childValue = data_get(
                                    $storedChildren,
                                    $option->id,
                                    ''
                                );

                                $childErrorKey =
                                    "answers.{$question->id}.child.{$option->id}";

                                $hasChildError =
                                    $errors->has(
                                        $childErrorKey
                                    );
                            @endphp


                            <div
                                data-option-item
                                class="rounded-lg border
                                       border-gray-200
                                       bg-white transition
                                       hover:border-indigo-300
                                       hover:bg-indigo-50/40"
                            >

                                <label
                                    for="option-{{ $question->id }}-{{ $option->id }}"

                                    class="flex cursor-pointer
                                           items-start gap-3 p-4"
                                >

                                    <input
                                        id="option-{{ $question->id }}-{{ $option->id }}"

                                        type="checkbox"

                                        name="answers[{{ $question->id }}][value][]"

                                        value="{{ $option->id }}"

                                        data-option-input

                                        data-has-child="{{ $hasChild ? '1' : '0' }}"

                                        data-child-target="child-{{ $question->id }}-{{ $option->id }}"

                                        @checked($isChecked)

                                        class="mt-0.5 h-4 w-4
                                               rounded border-gray-300
                                               text-indigo-600
                                               focus:ring-indigo-500"
                                    >


                                    <span
                                        class="text-sm font-medium
                                               text-gray-700"
                                    >
                                        {{ $option->answer_text }}
                                    </span>

                                </label>


                                @if ($hasChild)

                                    <div
                                        id="child-{{ $question->id }}-{{ $option->id }}"

                                        data-child-container

                                        class="{{ $isChecked ? '' : 'hidden' }}
                                               border-t border-gray-200
                                               px-4 py-4"
                                    >

                                        @if (
                                            filled(
                                                $option->answer_text2
                                            )
                                        )

                                            <label
                                                for="child-answer-{{ $question->id }}-{{ $option->id }}"

                                                class="mb-2 block
                                                       text-sm font-medium
                                                       text-gray-700"
                                            >
                                                {{ $option->answer_text2 }}
                                            </label>

                                        @endif


                                        <textarea
                                            id="child-answer-{{ $question->id }}-{{ $option->id }}"

                                            name="answers[{{ $question->id }}][child][{{ $option->id }}]"

                                            rows="3"

                                            data-child-input

                                            @if ($useMeaningfulValidation)
                                                data-meaningful-answer
                                                data-answer-type="child"
                                                data-answer-label="Jawaban tambahan"
                                            @endif

                                            @required($isChecked)

                                            @disabled(!$isChecked)

                                            maxlength="5000"

                                            placeholder="{{ $option->answer_text2 ?: 'Tulis jawaban tambahan...' }}"

                                            class="w-full rounded-lg
                                                   border
                                                   bg-white
                                                   px-4 py-3
                                                   text-sm outline-none
                                                   transition
                                                   focus:border-indigo-500
                                                   focus:ring-2
                                                   focus:ring-indigo-100
                                                   {{ $hasChildError
                                                        ? 'border-red-500 ring-2 ring-red-100'
                                                        : 'border-gray-300'
                                                   }}"
                                        >{{ $childValue }}</textarea>


                                        @error($childErrorKey)
                                            <p
                                                class="mt-2 text-sm
                                                       font-medium
                                                       text-red-600"
                                                data-meaningful-error
                                            >
                                                <i
                                                    class="fa-solid
                                                           fa-circle-exclamation
                                                           mr-1"
                                                ></i>

                                                {{ $message }}
                                            </p>
                                        @enderror

                                    </div>

                                @endif

                            </div>


                        @empty

                            <div
                                class="rounded-lg border
                                       border-dashed
                                       border-gray-300
                                       p-5 text-center
                                       text-sm text-gray-500"
                            >
                                Pilihan jawaban belum tersedia.
                            </div>

                        @endforelse

                    </div>


                {{-- ======================================================== --}}
                {{-- TYPE 6 / 9: NUMBER --}}
                {{-- ======================================================== --}}

                @elseif (
                    in_array(
                        $questionTypeId,
                        [6, 9],
                        true
                    )
                )

                    <div class="relative">

                        <div
                            class="pointer-events-none
                                   absolute inset-y-0 left-0
                                   flex items-center
                                   pl-4 text-gray-400"
                        >
                            <i class="fa-solid fa-hashtag"></i>
                        </div>


                        <input
                            id="answer-{{ $question->id }}"

                            type="number"

                            name="answers[{{ $question->id }}][value]"

                            value="{{ $storedValue }}"

                            required

                            inputmode="numeric"

                            placeholder="Masukkan nilai angka..."

                            class="w-full rounded-lg
                                   border border-gray-300
                                   py-3 pl-11 pr-4
                                   text-sm outline-none
                                   transition
                                   focus:border-indigo-500
                                   focus:ring-2
                                   focus:ring-indigo-100"
                        >

                    </div>


                {{-- ======================================================== --}}
                {{-- TYPE 7: DATE --}}
                {{-- ======================================================== --}}

                @elseif ($questionTypeId === 7)

                    <input
                        id="answer-{{ $question->id }}"

                        type="date"

                        name="answers[{{ $question->id }}][value]"

                        value="{{ $storedValue }}"

                        required

                        class="w-full rounded-lg
                               border border-gray-300
                               px-4 py-3
                               text-sm outline-none
                               transition
                               focus:border-indigo-500
                               focus:ring-2
                               focus:ring-indigo-100"
                    >


                {{-- ======================================================== --}}
                {{-- TYPE 8: EMAIL --}}
                {{-- ======================================================== --}}

                @elseif ($questionTypeId === 8)

                    <div class="relative">

                        <div
                            class="pointer-events-none
                                   absolute inset-y-0 left-0
                                   flex items-center
                                   pl-4 text-gray-400"
                        >
                            <i class="fa-regular fa-envelope"></i>
                        </div>


                        <input
                            id="answer-{{ $question->id }}"

                            type="email"

                            name="answers[{{ $question->id }}][value]"

                            value="{{ $storedValue }}"

                            required

                            autocomplete="email"

                            placeholder="nama@email.com"

                            class="w-full rounded-lg
                                   border border-gray-300
                                   py-3 pl-11 pr-4
                                   text-sm outline-none
                                   transition
                                   focus:border-indigo-500
                                   focus:ring-2
                                   focus:ring-indigo-100"
                        >

                    </div>


                {{-- ======================================================== --}}
                {{-- UNKNOWN TYPE --}}
                {{-- ======================================================== --}}

                @else

                    <div
                        class="rounded-lg border
                               border-red-200
                               bg-red-50 p-4
                               text-sm text-red-600"
                    >
                        Tipe pertanyaan
                        {{ $questionTypeId }}
                        belum didukung.
                    </div>

                @endif


                {{-- ======================================================== --}}
                {{-- VALIDATION MESSAGE --}}
                {{-- ======================================================== --}}

                <p
                    data-question-error
                    class="mt-3 hidden
                           text-sm font-medium
                           text-red-600"
                >
                    Pertanyaan ini wajib diisi.
                </p>

            </div>

        @endif

    @empty

        @include('user.survey.partials.empty', [
            'message' =>
                'Form ini belum memiliki pertanyaan aktif.',
        ])

    @endforelse

</div>
