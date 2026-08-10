@php
    $scaleValues = range(
        1,
        $maximumScale
    );
@endphp

<div class="space-y-6">
    @forelse (
        $questions->groupBy('no_header')
        as $header => $questionGroup
    )
        <div class="space-y-5">
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

                    $storedValue = old(
                        "answers.{$question->id}.value",
                        data_get(
                            $storedAnswer,
                            'value'
                        )
                    );
                @endphp

                {{-- TYPE 1: JUDUL --}}
                @if ($questionTypeId === 1)
                    <div
                        class="overflow-hidden rounded-xl
                               border border-indigo-200
                               bg-indigo-50 shadow-sm"
                    >
                        <div
                            class="flex items-start gap-4
                                   px-6 py-5"
                        >
                            <span
                                class="inline-flex h-11 w-11
                                       shrink-0 items-center
                                       justify-center rounded-xl
                                       bg-indigo-100 text-indigo-600"
                            >
                                <i class="fa-solid fa-list-check"></i>
                            </span>

                            <div class="flex-1">
                                <div
                                    class="text-xs font-semibold
                                           uppercase tracking-wide
                                           text-indigo-500"
                                >
                                    Petunjuk Pertanyaan
                                </div>

                                <h2
                                    class="mt-1 text-lg
                                           font-bold text-gray-900"
                                >
                                    {{ $question->name }}
                                </h2>
                            </div>
                        </div>
                    </div>

                    @continue
                @endif

                {{-- TYPE 2: PERTANYAAN --}}
                @if ($questionTypeId === 2)
                    <div
                        data-question-container
                        data-question-id="{{ $question->id }}"
                        class="overflow-hidden rounded-xl
                               border border-gray-200
                               bg-white shadow-sm"
                    >
                        {{-- QUESTION HEADER --}}
                        <div
                            class="border-b border-gray-200
                                   px-5 py-4"
                        >
                            <div class="flex items-start gap-3">
                                <span
                                    class="inline-flex h-9 min-w-9
                                           shrink-0 items-center
                                           justify-center rounded-lg
                                           bg-purple-100 px-2
                                           text-sm font-semibold
                                           text-purple-700"
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
                                        Pilih satu nilai yang paling sesuai.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- SCALE --}}
                        <div class="px-5 py-6">
                            <div
                                data-option-group
                                class="rounded-xl border
                                       border-purple-200
                                       bg-purple-50 px-5 py-6"
                            >
                                <div
                                    class="flex flex-wrap
                                           items-center justify-center
                                           gap-3 md:gap-4"
                                >
                                    @foreach ($scaleValues as $value)
                                        <label
                                            for="engagement-{{ $question->id }}-{{ $value }}"
                                            class="cursor-pointer"
                                        >
                                            <input
                                                id="engagement-{{ $question->id }}-{{ $value }}"
                                                type="radio"
                                                name="answers[{{ $question->id }}][value]"
                                                value="{{ $value }}"
                                                @checked(
                                                    (string) $storedValue ===
                                                    (string) $value
                                                )
                                                required
                                                class="peer sr-only"
                                            >

                                            <span
                                                class="inline-flex h-11 w-11
                                                       items-center
                                                       justify-center
                                                       rounded-full border
                                                       border-purple-300
                                                       bg-white text-sm
                                                       font-medium
                                                       text-purple-700
                                                       transition
                                                       hover:border-purple-500
                                                       hover:bg-purple-100
                                                       peer-checked:border-purple-600
                                                       peer-checked:bg-purple-600
                                                       peer-checked:text-white
                                                       peer-focus:ring-2
                                                       peer-focus:ring-purple-200"
                                            >
                                                {{ $value }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>


                            </div>

                            {{-- VALIDATION --}}
                            <p
                                data-question-error
                                class="mt-3 hidden text-sm
                                       font-medium text-red-600"
                            >
                                Pilih salah satu nilai.
                            </p>
                        </div>
                    </div>

                    @continue
                @endif

                {{-- UNKNOWN QUESTION TYPE --}}
                <div
                    class="rounded-xl border border-red-200
                           bg-red-50 px-5 py-4
                           text-sm text-red-600"
                >
                    Question Type {{ $questionTypeId }}
                    belum didukung pada Form Keterikatan.
                </div>
            @endforeach
        </div>
    @empty
        @include(
            'user.survey.partials.empty',
            [
                'message' =>
                    'Form Keterikatan belum memiliki pertanyaan aktif.',
            ]
        )
    @endforelse
</div>
