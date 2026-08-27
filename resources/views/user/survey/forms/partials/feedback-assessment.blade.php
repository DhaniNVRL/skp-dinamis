@php
    $feedbackColumnClass = match (
        count($feedbackFields)
    ) {
        3 => 'lg:grid-cols-3',
        2 => 'lg:grid-cols-2',
        default => 'grid-cols-1',
    };

    /*
    |--------------------------------------------------------------------------
    | Meaningful Answer Validation
    |--------------------------------------------------------------------------
    |
    | Validasi khusus:
    |
    | - Keunggulan
    | - Keluhan
    | - Saran
    |
    | hanya berlaku untuk role_id 2 dan 4.
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

                $activeSubunitIds = collect(
                    $activeMapSubUnit[
                        $form->id . '-' . $question->id
                    ] ?? []
                )
                    ->map(
                        fn ($id) => (int) $id
                    )
                    ->unique()
                    ->values();
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
                                   shrink-0 items-center
                                   justify-center rounded-xl
                                   bg-indigo-100 text-indigo-600"
                        >
                            <i class="fa-solid fa-comments"></i>
                        </span>


                        <div>

                            <div
                                class="text-xs font-semibold
                                       uppercase tracking-wide
                                       text-indigo-500"
                            >
                                Petunjuk Pertanyaan
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
            {{-- HANYA TYPE 2 --}}
            {{-- ================================================================ --}}

            @continue($questionTypeId !== 2)

            @continue(
                $activeSubunitIds->isEmpty()
            )


            <section
                class="overflow-hidden rounded-xl
                       border border-gray-200
                       bg-white shadow-sm"
            >

                @foreach (
                    $activeSubunitIds
                    as $subunitId
                )

                    @php
                        $subunit = $subunits->firstWhere(
                            'id',
                            $subunitId
                        );

                        $storedAnswer = data_get(
                            $answerMap,
                            "{$question->id}.{$subunitId}.0",
                            []
                        );
                    @endphp


                    @continue(!$subunit)


                    <div
                        data-question-container
                        data-question-id="{{ $question->id }}"
                        data-subunit-id="{{ $subunitId }}"
                        data-feedback-container

                        class="border border-gray-200
                               bg-white first:border-0"
                    >

                        {{-- ==================================================== --}}
                        {{-- SUB UNIT HEADER --}}
                        {{-- ==================================================== --}}

                        <div
                            class="flex items-center gap-3
                                   border-b border-blue-200
                                   bg-blue-50 px-5 py-4"
                        >

                            <span
                                class="inline-flex h-10 w-10
                                       shrink-0 items-center
                                       justify-center rounded-lg
                                       bg-blue-100 text-blue-600"
                            >
                                <i class="fa-solid fa-building"></i>
                            </span>


                            <div>

                                <div
                                    class="text-xs font-semibold
                                           uppercase tracking-wide
                                           text-blue-600"
                                >
                                    Sub Unit
                                </div>


                                <div
                                    class="font-bold text-gray-900"
                                >
                                    {{ $subunit->name }}
                                </div>

                            </div>

                        </div>


                        <div class="p-5">

                            {{-- ================================================= --}}
                            {{-- QUESTION --}}
                            {{-- ================================================= --}}

                            <div
                                class="mb-5 flex items-start gap-3
                                       rounded-xl border
                                       border-gray-200
                                       bg-white px-5 py-4"
                            >

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
                                        class="font-semibold
                                               leading-relaxed
                                               text-gray-900"
                                    >
                                        {{ $question->name }}
                                    </h3>


                                    <p
                                        class="mt-1 text-xs
                                               text-gray-500"
                                    >
                                        Lengkapi seluruh bagian
                                        jawaban di bawah ini.
                                    </p>

                                </div>

                            </div>


                            {{-- ================================================= --}}
                            {{-- FEEDBACK BLOCKS --}}
                            {{-- ================================================= --}}

                            <div
                                class="grid grid-cols-1
                                       items-stretch gap-4
                                       {{ $feedbackColumnClass }}"
                            >

                                @foreach (
                                    $feedbackFields
                                    as $field
                                )

                                    @php
                                        $fieldKey =
                                            $field['key'];

                                        $fieldValue = old(
                                            "answers.{$question->id}.{$subunitId}.{$fieldKey}",
                                            data_get(
                                                $storedAnswer,
                                                $fieldKey
                                            )
                                        );

                                        $fieldErrorKey =
                                            "answers.{$question->id}.{$subunitId}.{$fieldKey}";

                                        $hasFieldError =
                                            $errors->has(
                                                $fieldErrorKey
                                            );
                                    @endphp


                                    <div
                                        class="flex h-full flex-col
                                               rounded-xl border p-4
                                               {{ $field['wrapperClass'] }}"
                                    >

                                        <label
                                            for="feedback-{{ $question->id }}-{{ $subunitId }}-{{ $fieldKey }}"

                                            class="mb-3 flex items-center
                                                   gap-2 font-semibold
                                                   {{ $field['labelClass'] }}"
                                        >

                                            <i
                                                class="{{ $field['icon'] }}"
                                            ></i>

                                            {{ $field['label'] }}

                                        </label>


                                        <textarea
                                            id="feedback-{{ $question->id }}-{{ $subunitId }}-{{ $fieldKey }}"

                                            name="answers[{{ $question->id }}][{{ $subunitId }}][{{ $fieldKey }}]"

                                            rows="5"

                                            required

                                            maxlength="5000"

                                            @if ($useMeaningfulValidation)

                                                data-meaningful-answer

                                                data-answer-type="feedback"

                                                data-answer-label="{{ $field['label'] }}"

                                                data-feedback-question-id="{{ $question->id }}"

                                                data-feedback-subunit-id="{{ $subunitId }}"

                                                data-feedback-field="{{ $fieldKey }}"

                                            @endif

                                            placeholder="{{ $field['placeholder'] }}"

                                            class="min-h-32 w-full flex-1
                                                   resize-y rounded-lg
                                                   border
                                                   bg-white px-4 py-3
                                                   text-sm outline-none
                                                   transition
                                                   focus:ring-2

                                                   {{ $hasFieldError
                                                        ? 'border-red-500 ring-2 ring-red-100'
                                                        : 'border-gray-300'
                                                   }}

                                                   {{ $field['focusClass'] }}"
                                        >{{ $fieldValue }}</textarea>


                                        {{-- ===================================== --}}
                                        {{-- SERVER VALIDATION ERROR --}}
                                        {{-- ===================================== --}}

                                        @error($fieldErrorKey)

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

                                @endforeach

                            </div>


                            {{-- ================================================= --}}
                            {{-- VALIDATION ERROR --}}
                            {{-- ================================================= --}}

                            <p
                                data-question-error

                                class="mt-3 hidden
                                       text-sm font-medium
                                       text-red-600"
                            >
                                Seluruh bagian jawaban wajib diisi.
                            </p>

                        </div>

                    </div>

                @endforeach

            </section>

        @endforeach


    @empty

        @include(
            'user.survey.partials.empty',
            [
                'message' =>
                    'Form ini belum memiliki pertanyaan aktif.',
            ]
        )

    @endforelse

</div>
