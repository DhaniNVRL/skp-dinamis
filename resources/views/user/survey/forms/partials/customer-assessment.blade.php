@php
    $scaleValues = array_merge(
        range(1, $maximumScale),
        [0]
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
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values();
            @endphp

            {{-- QUESTION TYPE 1: TITLE --}}
            @if ($questionTypeId === 1)
                <div
                    class="rounded-xl border border-blue-200
                           bg-blue-50 px-5 py-4"
                >
                    <h2
                        class="text-center text-lg
                               font-bold text-gray-800"
                    >
                        {{ $question->name }}
                    </h2>
                </div>

                @continue
            @endif

            @continue($activeSubunitIds->isEmpty())

            <section
                class="overflow-hidden rounded-xl
                       border border-gray-200
                       bg-white shadow-sm"
            >
                {{-- QUESTION HEADER --}}
                <header
                    class="border-b border-gray-200
                           px-5 py-4"
                >
                    <div class="flex items-start gap-3">
                        <span
                            class="inline-flex h-9 min-w-9
                                   shrink-0 items-center
                                   justify-center rounded-lg
                                   bg-blue-100 px-2 text-sm
                                   font-semibold text-blue-700"
                        >
                            {{ $questionNumber }}
                        </span>

                        <div>
                            <h3 class="font-semibold text-gray-900">
                                {{ $question->name }}
                            </h3>

                            <p class="mt-1 text-xs text-gray-500">
                                @if ($questionTypeId === 2)
                                    Penilaian Kepentingan dan Kinerja
                                @elseif ($questionTypeId === 3)
                                    Penilaian Kepentingan dan Kinerja dengan alasan
                                @elseif ($questionTypeId === 4)
                                    Penilaian Kepentingan dan Kinerja dengan pilihan alasan
                                @elseif ($questionTypeId === 5)
                                    Penilaian satu indikator
                                @elseif ($questionTypeId === 6)
                                    Jawaban penilaian
                                @endif
                            </p>
                        </div>
                    </div>
                </header>

                <div class="space-y-5 bg-gray-50 p-5">
                    @foreach ($activeSubunitIds as $subunitId)
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

                            $storedImportance = old(
                                "answers.{$question->id}.{$subunitId}.importance",
                                data_get(
                                    $storedAnswer,
                                    'importance'
                                )
                            );

                            $storedPerformance = old(
                                "answers.{$question->id}.{$subunitId}.performance",
                                data_get(
                                    $storedAnswer,
                                    'performance'
                                )
                            );

                            $storedReason = old(
                                "answers.{$question->id}.{$subunitId}.reason",
                                data_get(
                                    $storedAnswer,
                                    'reason'
                                )
                            );

                            $storedReasonOptions = old(
                                "answers.{$question->id}.{$subunitId}.reasons",
                                data_get(
                                    $storedAnswer,
                                    'reasons',
                                    []
                                )
                            );

                            $storedChildren = old(
                                "answers.{$question->id}.{$subunitId}.children",
                                data_get(
                                    $storedAnswer,
                                    'children',
                                    []
                                )
                            );

                            $storedValue = old(
                                "answers.{$question->id}.{$subunitId}.value",
                                data_get(
                                    $storedAnswer,
                                    'value'
                                )
                            );

                            $showReason =
                                filled($storedPerformance) &&
                                (int) $storedPerformance !== 0 &&
                                (int) $storedPerformance <=
                                    $reasonMaximum;
                        @endphp

                        @continue(!$subunit)

                        <div
                            data-question-container
                            data-customer-assessment
                            data-question-type="{{ $questionTypeId }}"
                            data-reason-maximum="{{ $reasonMaximum }}"
                            class="rounded-xl border
                                   border-gray-200 bg-white"
                        >
                            {{-- SUB UNIT HEADER --}}
                            <div
                                class="flex items-center gap-3
                                       border-b border-blue-200
                                       bg-blue-50 px-5 py-4"
                            >
                                <span
                                    class="inline-flex h-10 w-10
                                           items-center justify-center
                                           rounded-lg bg-blue-100
                                           text-blue-600"
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

                                    <div class="font-bold text-gray-900">
                                        {{ $subunit->name }}
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-5 p-5">

                                {{-- IMPORTANCE AND PERFORMANCE --}}
                                @if (
                                    in_array(
                                        $questionTypeId,
                                        [2, 3, 4],
                                        true
                                    )
                                )
                                    <div
                                        class="grid grid-cols-1
                                               gap-5 lg:grid-cols-2"
                                    >
                                        {{-- IMPORTANCE --}}
                                        <div
                                            class="rounded-xl border
                                                   border-blue-200
                                                   bg-blue-50 p-5"
                                        >
                                            <div
                                                class="mb-4 text-center
                                                       font-semibold
                                                       text-blue-700"
                                            >
                                                Kepentingan
                                            </div>

                                            <div
                                                data-option-group
                                                class="flex flex-wrap
                                                       items-center
                                                       justify-center gap-3"
                                            >
                                                @foreach ($scaleValues as $value)
                                                    @if ($value === 0)
                                                        <div
                                                            class="mx-2 h-9
                                                                   border-l
                                                                   border-blue-300"
                                                        ></div>
                                                    @endif

                                                    <label class="cursor-pointer">
                                                        <input
                                                            type="radio"
                                                            name="answers[{{ $question->id }}][{{ $subunitId }}][importance]"
                                                            value="{{ $value }}"
                                                            @checked(
                                                                (string) $storedImportance ===
                                                                (string) $value
                                                            )
                                                            required
                                                            class="peer sr-only"
                                                        >

                                                        <span
                                                            class="inline-flex
                                                                   h-10 w-10
                                                                   items-center
                                                                   justify-center
                                                                   rounded-full
                                                                   border
                                                                   border-blue-300
                                                                   bg-white text-sm
                                                                   text-blue-700
                                                                   transition
                                                                   peer-checked:border-blue-600
                                                                   peer-checked:bg-blue-600
                                                                   peer-checked:text-white"
                                                        >
                                                            {{ $value }}
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- PERFORMANCE --}}
                                        <div
                                            class="rounded-xl border
                                                   border-emerald-200
                                                   bg-emerald-50 p-5"
                                        >
                                            <div
                                                class="mb-4 text-center
                                                       font-semibold
                                                       text-emerald-700"
                                            >
                                                Kinerja
                                            </div>

                                            <div
                                                data-option-group
                                                class="flex flex-wrap
                                                       items-center
                                                       justify-center gap-3"
                                            >
                                                @foreach ($scaleValues as $value)
                                                    @if ($value === 0)
                                                        <div
                                                            class="mx-2 h-9
                                                                   border-l
                                                                   border-emerald-300"
                                                        ></div>
                                                    @endif

                                                    <label class="cursor-pointer">
                                                        <input
                                                            type="radio"
                                                            name="answers[{{ $question->id }}][{{ $subunitId }}][performance]"
                                                            value="{{ $value }}"
                                                            data-performance-input
                                                            @checked(
                                                                (string) $storedPerformance ===
                                                                (string) $value
                                                            )
                                                            required
                                                            class="peer sr-only"
                                                        >

                                                        <span
                                                            class="inline-flex
                                                                   h-10 w-10
                                                                   items-center
                                                                   justify-center
                                                                   rounded-full
                                                                   border
                                                                   border-emerald-300
                                                                   bg-white text-sm
                                                                   text-emerald-700
                                                                   transition
                                                                   peer-checked:border-emerald-600
                                                                   peer-checked:bg-emerald-600
                                                                   peer-checked:text-white"
                                                        >
                                                            {{ $value }}
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- TYPE 3: TEXTAREA REASON --}}
                                @if ($questionTypeId === 3)
                                    <div
                                        data-performance-reason
                                        class="{{ $showReason ? '' : 'hidden' }}
                                               rounded-xl border
                                               border-amber-200
                                               bg-amber-50 p-5"
                                    >
                                        <div class="mb-4">
                                            <h4
                                                class="font-semibold
                                                       text-gray-900"
                                            >
                                                Alasan Penilaian Kinerja
                                            </h4>

                                            <p
                                                class="mt-1 text-sm
                                                       text-gray-500"
                                            >
                                                Wajib diisi jika nilai
                                                Kinerja 1–{{ $reasonMaximum }}.
                                            </p>
                                        </div>

                                        <textarea
                                            name="answers[{{ $question->id }}][{{ $subunitId }}][reason]"
                                            rows="4"
                                            data-performance-reason-input
                                            @required($showReason)
                                            @disabled(!$showReason)
                                            placeholder="Tuliskan alasan penilaian Kinerja..."
                                            class="w-full rounded-lg
                                                   border border-gray-300
                                                   px-4 py-3 text-sm
                                                   outline-none
                                                   focus:border-amber-500
                                                   focus:ring-2
                                                   focus:ring-amber-100"
                                        >{{ $storedReason }}</textarea>
                                    </div>
                                @endif

                                {{-- TYPE 4: CHECKBOX REASONS --}}
                                @if ($questionTypeId === 4)
                                    <div
                                        data-performance-reason
                                        class="{{ $showReason ? '' : 'hidden' }}
                                               rounded-xl border
                                               border-amber-200
                                               bg-amber-50 p-5"
                                    >
                                        <div class="mb-4">
                                            <h4 class="font-semibold text-gray-900">
                                                Pilihan Alasan
                                            </h4>

                                            <p class="mt-1 text-sm text-gray-500">
                                                Pilih minimal satu alasan jika
                                                Kinerja 1–{{ $reasonMaximum }}.
                                            </p>
                                        </div>

                                        <div
                                            data-reason-checkbox-group
                                            @if ($showReason)
                                                data-required-group
                                            @endif
                                            class="space-y-3"
                                        >
                                            @forelse ($question->options as $option)
                                                @php
                                                    $optionChecked = in_array(
                                                        (string) $option->id,
                                                        array_map(
                                                            'strval',
                                                            (array) $storedReasonOptions
                                                        ),
                                                        true
                                                    );

                                                    $hasChild =
                                                        (int) $option->has_child === 1;

                                                    $childValue = data_get(
                                                        $storedChildren,
                                                        $option->id,
                                                        ''
                                                    );
                                                @endphp

                                                <div
                                                    class="overflow-hidden
                                                           rounded-lg border
                                                           border-gray-200
                                                           bg-white"
                                                >
                                                    <label
                                                        class="flex cursor-pointer
                                                               items-start gap-3 p-4"
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            name="answers[{{ $question->id }}][{{ $subunitId }}][reasons][]"
                                                            value="{{ $option->id }}"
                                                            data-option-input
                                                            data-has-child="{{ $hasChild ? 1 : 0 }}"
                                                            data-child-target="customer-child-{{ $question->id }}-{{ $subunitId }}-{{ $option->id }}"
                                                            @checked($optionChecked)
                                                            @disabled(!$showReason)
                                                            class="mt-0.5 h-4 w-4
                                                                   rounded border-gray-300
                                                                   text-indigo-600
                                                                   focus:ring-indigo-500"
                                                        >

                                                        <span
                                                            class="text-sm
                                                                   font-medium
                                                                   text-gray-700"
                                                        >
                                                            {{ $option->answer_text }}
                                                        </span>
                                                    </label>

                                                    @if ($hasChild)
                                                        <div
                                                            id="customer-child-{{ $question->id }}-{{ $subunitId }}-{{ $option->id }}"
                                                            data-child-container
                                                            class="{{ $showReason && $optionChecked ? '' : 'hidden' }}
                                                                   border-t
                                                                   border-gray-200 p-4"
                                                        >
                                                            @if (filled($option->answer_text2))
                                                                <label
                                                                    class="mb-2 block
                                                                           text-sm
                                                                           font-medium
                                                                           text-gray-700"
                                                                >
                                                                    {{ $option->answer_text2 }}
                                                                </label>
                                                            @endif

                                                            <textarea
                                                                name="answers[{{ $question->id }}][{{ $subunitId }}][children][{{ $option->id }}]"
                                                                rows="3"
                                                                data-child-input
                                                                @required(
                                                                    $showReason &&
                                                                    $optionChecked
                                                                )
                                                                @disabled(
                                                                    !$showReason ||
                                                                    !$optionChecked
                                                                )
                                                                placeholder="Tulis jawaban tambahan..."
                                                                class="w-full
                                                                       rounded-lg
                                                                       border
                                                                       border-gray-300
                                                                       px-4 py-3
                                                                       text-sm
                                                                       outline-none
                                                                       focus:border-indigo-500
                                                                       focus:ring-2
                                                                       focus:ring-indigo-100"
                                                            >{{ $childValue }}</textarea>
                                                        </div>
                                                    @endif
                                                </div>
                                            @empty
                                                <div
                                                    class="rounded-lg
                                                           border border-dashed
                                                           border-gray-300
                                                           p-5 text-center
                                                           text-sm text-gray-500"
                                                >
                                                    Pilihan alasan belum tersedia.
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                @endif

                                {{-- TYPE 5: SINGLE INDICATOR --}}
                                @if ($questionTypeId === 5)
                                    <div
                                        data-option-group
                                        class="flex flex-wrap
                                               items-center justify-center
                                               gap-3 rounded-xl border
                                               border-indigo-200
                                               bg-indigo-50 p-5"
                                    >
                                        @foreach ($scaleValues as $value)
                                            @if ($value === 0)
                                                <div
                                                    class="mx-2 h-9
                                                           border-l
                                                           border-indigo-300"
                                                ></div>
                                            @endif

                                            <label class="cursor-pointer">
                                                <input
                                                    type="radio"
                                                    name="answers[{{ $question->id }}][{{ $subunitId }}][value]"
                                                    value="{{ $value }}"
                                                    @checked(
                                                        (string) $storedValue ===
                                                        (string) $value
                                                    )
                                                    required
                                                    class="peer sr-only"
                                                >

                                                <span
                                                    class="inline-flex h-10 w-10
                                                           items-center
                                                           justify-center
                                                           rounded-full border
                                                           border-indigo-300
                                                           bg-white text-sm
                                                           text-indigo-700
                                                           peer-checked:border-indigo-600
                                                           peer-checked:bg-indigo-600
                                                           peer-checked:text-white"
                                                >
                                                    {{ $value }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- TYPE 6: TEXTAREA --}}
                                @if ($questionTypeId === 6)
                                    <textarea
                                        name="answers[{{ $question->id }}][{{ $subunitId }}][value]"
                                        rows="4"
                                        required
                                        placeholder="Tulis jawaban..."
                                        class="w-full rounded-lg border
                                               border-gray-300 px-4 py-3
                                               text-sm outline-none
                                               focus:border-indigo-500
                                               focus:ring-2
                                               focus:ring-indigo-100"
                                    >{{ $storedValue }}</textarea>
                                @endif

                                <p
                                    data-question-error
                                    class="hidden text-sm
                                           font-medium text-red-600"
                                >
                                    Pertanyaan ini wajib diisi.
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    @empty
        @include('user.survey.partials.empty', [
            'message' =>
                'Form ini belum memiliki pertanyaan aktif.',
        ])
    @endforelse
</div>