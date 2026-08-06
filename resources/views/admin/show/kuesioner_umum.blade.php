    @php
        $subunitList = $subunitIds->toArray();
    @endphp

    {{-- FORM DESCRIPTION --}}
    @if($form->description)
        <div class="relative bg-white rounded-xl shadow border border-gray-200 p-6 mb-6">

            {!! $form->description->content ?? '<div class="text-gray-400 italic">Belum ada description.</div>' !!}

        </div>
    @endif


    {{-- QUESTIONS --}}
    @foreach ($form->questions->sortBy('no') as $question)

        @php
            $key = $form->id.'-'.$question->id;

            // cek apakah ada relasi subunit_questions
            $hasRelation = isset($activeMapSubUnit[$key])
                && count(array_intersect($activeMapSubUnit[$key], $subunitList)) > 0;
        @endphp

        {{-- 🔥 FILTER UTAMA --}}
        @if($hasRelation)

            <div class="mb-6 p-4 border rounded-lg shadow-sm bg-white">

                {{-- HEADER --}}
                <div class="flex justify-between items-start gap-4">
                    <label class="font-semibold text-gray-800">
                        {{ $question->no_header }}{{ $question->no }}. {{ $question->name }}
                    </label>
                </div>

                {{-- BODY --}}
                <div class="mt-4">

                    {{-- TYPE 3 RADIO --}}
                    @if ($question->id_questiontypes == 3)

                        <div class="question-block space-y-2">

                            @foreach ($question->options as $opsion)
                                <div class="option-item flex flex-col p-3 border rounded-lg hover:bg-blue-50 transition">

                                    <div class="flex items-center justify-between">

                                        <label class="flex items-center gap-3 cursor-pointer w-full">
                                            <input type="radio"
                                                name="answer_{{ $question->id }}"
                                                value="{{ $opsion->id }}"
                                                data-has-child="{{ $opsion->has_child }}"
                                                class="option-radio">

                                            <span class="text-gray-700">
                                                {{ $opsion->answer_text }}
                                            </span>
                                        </label>

                                    </div>

                                    {{-- CHILD --}}
                                    <div class="child-textarea mt-2 hidden w-full answer-text2-wrapper">

                                        @if(!empty($opsion->answer_text2))
                                            <span class="text-red-600 block mb-2">
                                                {{ $opsion->answer_text2 }}
                                            </span>
                                        @endif

                                        <textarea
                                            name="child_answer_{{ $question->id }}[{{ $opsion->id }}]"
                                            class="border rounded p-2 w-full"
                                            placeholder="Tulis jawaban tambahan..."></textarea>

                                    </div>

                                </div>
                            @endforeach

                        </div>

                    {{-- TYPE 4 CHECKBOX --}}
                    @elseif ($question->id_questiontypes == 4)

                        <div class="question-block space-y-2">

                            @foreach ($question->options as $opsion)
                                <div class="option-item flex flex-col p-3 border rounded-lg hover:bg-blue-50 transition">

                                    <div class="flex items-center justify-between">

                                        <label class="flex items-center gap-3 cursor-pointer w-full">
                                            <input type="checkbox"
                                                name="answer_{{ $question->id }}[]"
                                                value="{{ $opsion->id }}"
                                                data-has-child="{{ $opsion->has_child }}"
                                                class="option-checkbox">

                                            <span class="text-gray-700">
                                                {{ $opsion->answer_text }}
                                            </span>
                                        </label>

                                    </div>

                                    {{-- CHILD --}}
                                    <div class="child-textarea mt-2 hidden w-full answer-text2-wrapper">

                                        @if(!empty($opsion->answer_text2))
                                            <span class="text-red-600 block mb-2">
                                                {{ $opsion->answer_text2 }}
                                            </span>
                                        @endif

                                        <textarea
                                            name="child_answer_{{ $question->id }}[{{ $opsion->id }}]"
                                            class="border rounded p-2 w-full"
                                            placeholder="Tulis jawaban tambahan..."></textarea>

                                    </div>

                                </div>
                            @endforeach

                        </div>

                    {{-- TYPE TEXT --}}
                    @elseif ($question->id_questiontypes == 1)

                        <input type="text"
                            name="answers[{{ $question->id }}]"
                            class="border p-2 w-full rounded">

                    {{-- TYPE TEXTAREA --}}
                    @elseif ($question->id_questiontypes == 2)

                        <textarea name="answers[{{ $question->id }}]"
                            class="border p-2 w-full rounded"></textarea>

                    {{-- NUMBER --}}
                    @elseif ($question->id_questiontypes == 6)

                        <input type="text"
                            name="answers[{{ $question->id }}]"
                            class="border p-2 w-full rounded validate-number">

                    {{-- DATE --}}
                    @elseif ($question->id_questiontypes == 7)

                        <input type="date"
                            name="answers[{{ $question->id }}]"
                            class="border p-2 w-full rounded">

                    {{-- EMAIL --}}
                    @elseif ($question->id_questiontypes == 8)

                        <input type="email"
                            name="answers[{{ $question->id }}]"
                            class="border p-2 w-full rounded validate-email">

                    {{-- NUMBER ALT --}}
                    @elseif ($question->id_questiontypes == 9)

                        <input type="text"
                            name="answers[{{ $question->id }}]"
                            class="border p-2 w-full rounded validate-number">

                    @else

                        <p class="text-red-500">Type belum dibuat</p>

                    @endif

                </div>
            </div>

        @endif

    @endforeach
