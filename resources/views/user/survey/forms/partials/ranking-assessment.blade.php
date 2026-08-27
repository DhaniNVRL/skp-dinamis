@php
    /*
    |--------------------------------------------------------------------------
    | Meaningful Answer Validation
    |--------------------------------------------------------------------------
    |
    | Validasi jawaban bermakna hanya berlaku untuk role_id 2 dan 4.
    |
    | Pada form Ranking, validasi ini HANYA berlaku pada child answer.
    |
    */
    $useMeaningfulValidation = in_array(
        (int) auth()->user()?->role_id,
        [2, 4],
        true
    );
@endphp


<div class="space-y-6">

    @forelse (
        $questions->groupBy('no_header')
        as $header => $questionGroup
    )

        @foreach ($questionGroup as $question)

            @php
                $questionTypeId =
                    (int) $question->questiontype_id;

                $questionNumber = trim(
                    ($question->no_header ?? '') .
                    ($question->no ?? '')
                );

                $storedAnswer = data_get(
                    $answerMap,
                    "{$question->id}.0.0",
                    []
                );

                $storedRankings = old(
                    "answers.{$question->id}.value",
                    data_get(
                        $storedAnswer,
                        'value',
                        []
                    )
                );
            @endphp


            {{-- ================================================================ --}}
            {{-- TYPE 1: JUDUL --}}
            {{-- ================================================================ --}}

            @if ($questionTypeId === 1)

                <div
                    class="rounded-xl border border-indigo-200
                           bg-indigo-50 px-6 py-5"
                >
                    <div class="flex items-start gap-4">

                        <span
                            class="inline-flex h-11 w-11
                                   shrink-0 items-center justify-center
                                   rounded-xl bg-indigo-100
                                   text-indigo-600"
                        >
                            <i class="fa-solid fa-ranking-star"></i>
                        </span>


                        <div>

                            <div
                                class="text-xs font-semibold uppercase
                                       tracking-wide text-indigo-500"
                            >
                                Petunjuk Ranking
                            </div>


                            <h2
                                class="mt-1 text-lg font-bold
                                       text-gray-900"
                            >
                                {{ $question->name }}
                            </h2>

                        </div>

                    </div>
                </div>

                @continue

            @endif


            {{-- ================================================================ --}}
            {{-- TYPE 2: PERTANYAAN RANKING --}}
            {{-- ================================================================ --}}

            @if ($questionTypeId === 2)

                <div
                    data-question-container
                    data-ranking-container
                    data-question-id="{{ $question->id }}"
                    class="overflow-hidden rounded-xl
                           border border-gray-200
                           bg-white shadow-sm"
                >

                    {{-- ======================================================== --}}
                    {{-- QUESTION HEADER --}}
                    {{-- ======================================================== --}}

                    <div
                        class="border-b border-gray-200
                               px-5 py-4"
                    >
                        <div class="flex items-start gap-3">

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


                            <div>

                                <h3
                                    class="font-semibold leading-relaxed
                                           text-gray-900"
                                >
                                    {{ $question->name }}
                                </h3>


                                <p class="mt-1 text-xs text-gray-500">
                                    Tentukan urutan Ranking 1 sampai
                                    Ranking {{ $maximumRank }}.
                                </p>

                            </div>

                        </div>
                    </div>


                    {{-- ======================================================== --}}
                    {{-- RANKING SELECTS --}}
                    {{-- ======================================================== --}}

                    <div class="p-5">

                        <div
                            class="grid grid-cols-1 gap-4
                                   md:grid-cols-2
                                   xl:grid-cols-{{ min($maximumRank, 3) }}"
                        >

                            @for (
                                $rank = 1;
                                $rank <= $maximumRank;
                                $rank++
                            )

                                @php
                                    /*
                                    |--------------------------------------------------------------------------
                                    | Selected Ranking
                                    |--------------------------------------------------------------------------
                                    */
                                    $selectedOptionId = data_get(
                                        $storedRankings,
                                        "{$rank}.option_id"
                                    );

                                    $selectedChild = data_get(
                                        $storedRankings,
                                        "{$rank}.child",
                                        ''
                                    );

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Selected Option
                                    |--------------------------------------------------------------------------
                                    */
                                    $selectedOption = $question
                                        ->options
                                        ->firstWhere(
                                            'id',
                                            (int) $selectedOptionId
                                        );

                                    $selectedHasChild =
                                        $selectedOption
                                        &&
                                        (int) $selectedOption->has_child === 1;

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Child Error
                                    |--------------------------------------------------------------------------
                                    */
                                    $childErrorKey =
                                        "answers.{$question->id}.value.{$rank}.child";

                                    $hasChildError =
                                        $errors->has(
                                            $childErrorKey
                                        );

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Option Error
                                    |--------------------------------------------------------------------------
                                    */
                                    $optionErrorKey =
                                        "answers.{$question->id}.value.{$rank}.option_id";

                                    $hasOptionError =
                                        $errors->has(
                                            $optionErrorKey
                                        );
                                @endphp


                                <div
                                    data-ranking-row
                                    class="rounded-xl border
                                           border-gray-200
                                           bg-gray-50 p-4"
                                >

                                    {{-- ================================================= --}}
                                    {{-- RANK LABEL --}}
                                    {{-- ================================================= --}}

                                    <label
                                        for="ranking-{{ $question->id }}-{{ $rank }}"
                                        class="mb-2 block text-sm
                                               font-semibold text-gray-800"
                                    >
                                        Ranking {{ $rank }}
                                    </label>


                                    {{-- ================================================= --}}
                                    {{-- RANK SELECT --}}
                                    {{-- ================================================= --}}

                                    <div class="relative">

                                        <select
                                            id="ranking-{{ $question->id }}-{{ $rank }}"

                                            name="answers[{{ $question->id }}][value][{{ $rank }}][option_id]"

                                            required

                                            data-ranking-select

                                            class="w-full appearance-none
                                                   rounded-lg border
                                                   bg-white
                                                   px-4 py-3 pr-10
                                                   text-sm outline-none
                                                   transition
                                                   focus:border-indigo-500
                                                   focus:ring-2
                                                   focus:ring-indigo-100
                                                   {{ $hasOptionError
                                                        ? 'border-red-500 ring-2 ring-red-100'
                                                        : 'border-gray-300'
                                                   }}"
                                        >

                                            <option value="">
                                                Pilih Ranking {{ $rank }}
                                            </option>


                                            @foreach (
                                                $question->options
                                                as $option
                                            )

                                                <option
                                                    value="{{ $option->id }}"

                                                    data-has-child="{{ (int) $option->has_child === 1 ? '1' : '0' }}"

                                                    data-answer-text2="{{ $option->answer_text2 ?? '' }}"

                                                    @selected(
                                                        (string) $selectedOptionId ===
                                                        (string) $option->id
                                                    )
                                                >
                                                    {{ $option->answer_text }}
                                                </option>

                                            @endforeach

                                        </select>


                                        <span
                                            class="pointer-events-none
                                                   absolute inset-y-0 right-0
                                                   flex items-center pr-4
                                                   text-gray-400"
                                        >
                                            <i class="fa-solid fa-chevron-down"></i>
                                        </span>

                                    </div>


                                    {{-- SERVER ERROR: RANKING --}}
                                    @error($optionErrorKey)

                                        <p
                                            class="mt-2 text-sm
                                                   font-medium text-red-600"
                                        >
                                            <i
                                                class="fa-solid
                                                       fa-circle-exclamation
                                                       mr-1"
                                            ></i>

                                            {{ $message }}
                                        </p>

                                    @enderror


                                    {{-- ================================================= --}}
                                    {{-- HAS CHILD --}}
                                    {{-- ================================================= --}}

                                    <div
                                        data-ranking-child

                                        class="{{ $selectedHasChild ? '' : 'hidden' }}
                                               mt-4"
                                    >

                                        @if (
                                            filled(
                                                $selectedOption?->answer_text2
                                            )
                                        )

                                            <label
                                                for="ranking-child-{{ $question->id }}-{{ $rank }}"

                                                data-ranking-child-label

                                                class="mb-2 block text-sm
                                                       font-medium text-gray-700"
                                            >
                                                {{ $selectedOption->answer_text2 }}
                                            </label>

                                        @else

                                            <label
                                                for="ranking-child-{{ $question->id }}-{{ $rank }}"

                                                data-ranking-child-label

                                                class="mb-2 block text-sm
                                                       font-medium text-gray-700"
                                            >
                                                Jawaban tambahan
                                            </label>

                                        @endif


                                        <textarea
                                            id="ranking-child-{{ $question->id }}-{{ $rank }}"

                                            name="answers[{{ $question->id }}][value][{{ $rank }}][child]"

                                            rows="3"

                                            data-ranking-child-input

                                            @if ($useMeaningfulValidation)
                                                data-meaningful-answer
                                                data-answer-type="child"
                                                data-answer-label="Jawaban tambahan Ranking {{ $rank }}"
                                            @endif

                                            @required($selectedHasChild)

                                            @disabled(!$selectedHasChild)

                                            maxlength="5000"

                                            placeholder="{{ $selectedOption?->answer_text2 ?: 'Tulis jawaban tambahan...' }}"

                                            class="w-full rounded-lg
                                                   border
                                                   bg-white px-4 py-3
                                                   text-sm outline-none
                                                   transition
                                                   focus:border-indigo-500
                                                   focus:ring-2
                                                   focus:ring-indigo-100
                                                   {{ $hasChildError
                                                        ? 'border-red-500 ring-2 ring-red-100'
                                                        : 'border-gray-300'
                                                   }}"
                                        >{{ $selectedChild }}</textarea>


                                        {{-- SERVER ERROR: CHILD ANSWER --}}
                                        @error($childErrorKey)

                                            <p
                                                class="mt-2 text-sm
                                                       font-medium text-red-600"
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

                                </div>

                            @endfor

                        </div>


                        {{-- ==================================================== --}}
                        {{-- QUESTION ERROR --}}
                        {{-- ==================================================== --}}

                        <p
                            data-question-error
                            class="mt-3 hidden text-sm
                                   font-medium text-red-600"
                        >
                            Seluruh urutan Ranking wajib dipilih.
                        </p>

                    </div>

                </div>

                @continue

            @endif


            {{-- ================================================================ --}}
            {{-- UNSUPPORTED TYPE --}}
            {{-- ================================================================ --}}

            <div
                class="rounded-xl border border-red-200
                       bg-red-50 p-4 text-sm text-red-600"
            >
                Question Type {{ $questionTypeId }}
                belum didukung pada Form Ranking.
            </div>

        @endforeach

    @empty

        @include(
            'user.survey.partials.empty',
            [
                'message' =>
                    'Form Ranking belum memiliki pertanyaan aktif.',
            ]
        )

    @endforelse

</div>
