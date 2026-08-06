<div class="space-y-5">
    @forelse ($questions->sortBy([
        ['no_header', 'asc'],
        ['no', 'asc'],
    ]) as $question)

        @php
            $questionTypeId = (int) $question->questiontype_id;

            $storedAnswer = data_get(
                $answerMap,
                "{$question->id}.0.0",
                []
            );

            $storedValue = old(
                "answers.{$question->id}.value",
                data_get($storedAnswer, 'value')
            );

            $storedChildren = old(
                "answers.{$question->id}.child",
                data_get($storedAnswer, 'child', [])
            );

            $questionNumber = trim(
                ($question->no_header ?? '') .
                ($question->no ?? '')
            );
        @endphp

        <div
            data-question-container
            data-question-id="{{ $question->id }}"
            data-question-type="{{ $questionTypeId }}"
            class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"
        >
            {{-- QUESTION HEADER --}}
            <div class="mb-5 flex items-start gap-3">
                <span
                    class="inline-flex h-9 min-w-9 shrink-0 items-center
                           justify-center rounded-lg bg-indigo-100 px-2
                           text-sm font-semibold text-indigo-700"
                >
                    {{ $questionNumber }}
                </span>

                <div class="min-w-0 flex-1">
                    <label class="font-semibold text-gray-900">
                        {{ $question->name }}
                    </label>

                    @if ($questionTypeId === 1)
                        <p class="mt-1 text-xs uppercase tracking-wide text-gray-400">
                            Jawaban singkat
                        </p>
                    @elseif ($questionTypeId === 2)
                        <p class="mt-1 text-xs uppercase tracking-wide text-gray-400">
                            Jawaban panjang
                        </p>
                    @elseif ($questionTypeId === 3)
                        <p class="mt-1 text-xs uppercase tracking-wide text-gray-400">
                            Pilih satu jawaban
                        </p>
                    @elseif ($questionTypeId === 4)
                        <p class="mt-1 text-xs uppercase tracking-wide text-gray-400">
                            Pilih satu atau beberapa jawaban
                        </p>
                    @elseif (in_array($questionTypeId, [6, 9], true))
                        <p class="mt-1 text-xs uppercase tracking-wide text-gray-400">
                            Jawaban angka
                        </p>
                    @elseif ($questionTypeId === 7)
                        <p class="mt-1 text-xs uppercase tracking-wide text-gray-400">
                            Pilih tanggal
                        </p>
                    @elseif ($questionTypeId === 8)
                        <p class="mt-1 text-xs uppercase tracking-wide text-gray-400">
                            Alamat email
                        </p>
                    @endif
                </div>
            </div>

            {{-- TYPE 1: SHORT TEXT --}}
            @if ($questionTypeId === 1)
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
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
                        class="w-full rounded-lg border border-gray-300
                               py-3 pl-11 pr-4 text-sm outline-none transition
                               focus:border-indigo-500 focus:ring-2
                               focus:ring-indigo-100"
                    >
                </div>

            {{-- TYPE 2: TEXTAREA --}}
            @elseif ($questionTypeId === 2)
                <textarea
                    id="answer-{{ $question->id }}"
                    name="answers[{{ $question->id }}][value]"
                    rows="4"
                    required
                    maxlength="5000"
                    placeholder="Tulis jawaban..."
                    class="w-full rounded-lg border border-gray-300
                           px-4 py-3 text-sm outline-none transition
                           focus:border-indigo-500 focus:ring-2
                           focus:ring-indigo-100"
                >{{ $storedValue }}</textarea>

            {{-- TYPE 3: RADIO --}}
            @elseif ($questionTypeId === 3)
                <div
                    data-option-group
                    data-option-type="radio"
                    class="space-y-3"
                >
                    @forelse ($question->options as $option)
                        @php
                            $hasChild = (int) $option->has_child === 1;

                            $isChecked = (string) $storedValue ===
                                (string) $option->id;

                            $childValue = data_get(
                                $storedChildren,
                                $option->id,
                                ''
                            );
                        @endphp

                        <div
                            data-option-item
                            class="rounded-lg border border-gray-200
                                   bg-white transition hover:border-indigo-300
                                   hover:bg-indigo-50/40"
                        >
                            <label
                                for="option-{{ $question->id }}-{{ $option->id }}"
                                class="flex cursor-pointer items-start gap-3 p-4"
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
                                    class="mt-0.5 h-4 w-4 border-gray-300
                                           text-indigo-600 focus:ring-indigo-500"
                                >

                                <span class="text-sm font-medium text-gray-700">
                                    {{ $option->answer_text }}
                                </span>
                            </label>

                            @if ($hasChild)
                                <div
                                    id="child-{{ $question->id }}-{{ $option->id }}"
                                    data-child-container
                                    class="{{ $isChecked ? '' : 'hidden' }}
                                           border-t border-gray-200 px-4 py-4"
                                >
                                    @if (filled($option->answer_text2))
                                        <label
                                            for="child-answer-{{ $question->id }}-{{ $option->id }}"
                                            class="mb-2 block text-sm font-medium text-gray-700"
                                        >
                                            {{ $option->answer_text2 }}
                                        </label>
                                    @endif

                                    <textarea
                                        id="child-answer-{{ $question->id }}-{{ $option->id }}"
                                        name="answers[{{ $question->id }}][child][{{ $option->id }}]"
                                        rows="3"
                                        data-child-input
                                        @required($isChecked)
                                        placeholder="Tulis jawaban tambahan..."
                                        class="w-full rounded-lg border border-gray-300
                                               px-4 py-3 text-sm outline-none transition
                                               focus:border-indigo-500 focus:ring-2
                                               focus:ring-indigo-100"
                                    >{{ $childValue }}</textarea>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-300 p-5 text-center text-sm text-gray-500">
                            Pilihan jawaban belum tersedia.
                        </div>
                    @endforelse
                </div>

            {{-- TYPE 4: CHECKBOX --}}
            @elseif ($questionTypeId === 4)
                @php
                    $checkedValues = is_array($storedValue)
                        ? array_map('strval', $storedValue)
                        : [];
                @endphp

                <div
                    data-option-group
                    data-option-type="checkbox"
                    data-required-group
                    class="space-y-3"
                >
                    @forelse ($question->options as $option)
                        @php
                            $hasChild = (int) $option->has_child === 1;

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
                        @endphp

                        <div
                            data-option-item
                            class="rounded-lg border border-gray-200
                                   bg-white transition hover:border-indigo-300
                                   hover:bg-indigo-50/40"
                        >
                            <label
                                for="option-{{ $question->id }}-{{ $option->id }}"
                                class="flex cursor-pointer items-start gap-3 p-4"
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
                                    class="mt-0.5 h-4 w-4 rounded border-gray-300
                                           text-indigo-600 focus:ring-indigo-500"
                                >

                                <span class="text-sm font-medium text-gray-700">
                                    {{ $option->answer_text }}
                                </span>
                            </label>

                            @if ($hasChild)
                                <div
                                    id="child-{{ $question->id }}-{{ $option->id }}"
                                    data-child-container
                                    class="{{ $isChecked ? '' : 'hidden' }}
                                           border-t border-gray-200 px-4 py-4"
                                >
                                    @if (filled($option->answer_text2))
                                        <label
                                            for="child-answer-{{ $question->id }}-{{ $option->id }}"
                                            class="mb-2 block text-sm font-medium text-gray-700"
                                        >
                                            {{ $option->answer_text2 }}
                                        </label>
                                    @endif

                                    <textarea
                                        id="child-answer-{{ $question->id }}-{{ $option->id }}"
                                        name="answers[{{ $question->id }}][child][{{ $option->id }}]"
                                        rows="3"
                                        data-child-input
                                        @required($isChecked)
                                        placeholder="Tulis jawaban tambahan..."
                                        class="w-full rounded-lg border border-gray-300
                                               px-4 py-3 text-sm outline-none transition
                                               focus:border-indigo-500 focus:ring-2
                                               focus:ring-indigo-100"
                                    >{{ $childValue }}</textarea>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-300 p-5 text-center text-sm text-gray-500">
                            Pilihan jawaban belum tersedia.
                        </div>
                    @endforelse
                </div>

            {{-- TYPE 6/9: NUMBER --}}
            @elseif (in_array($questionTypeId, [6, 9], true))
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
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
                        class="w-full rounded-lg border border-gray-300
                               py-3 pl-11 pr-4 text-sm outline-none transition
                               focus:border-indigo-500 focus:ring-2
                               focus:ring-indigo-100"
                    >
                </div>

            {{-- TYPE 7: DATE --}}
            @elseif ($questionTypeId === 7)
                <input
                    id="answer-{{ $question->id }}"
                    type="date"
                    name="answers[{{ $question->id }}][value]"
                    value="{{ $storedValue }}"
                    required
                    class="w-full rounded-lg border border-gray-300
                           px-4 py-3 text-sm outline-none transition
                           focus:border-indigo-500 focus:ring-2
                           focus:ring-indigo-100"
                >

            {{-- TYPE 8: EMAIL --}}
            @elseif ($questionTypeId === 8)
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
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
                        class="w-full rounded-lg border border-gray-300
                               py-3 pl-11 pr-4 text-sm outline-none transition
                               focus:border-indigo-500 focus:ring-2
                               focus:ring-indigo-100"
                    >
                </div>

            {{-- UNKNOWN TYPE --}}
            @else
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-600">
                    Tipe pertanyaan {{ $questionTypeId }} belum didukung.
                </div>
            @endif

            <p
                data-question-error
                class="mt-3 hidden text-sm font-medium text-red-600"
            >
                Pertanyaan ini wajib diisi.
            </p>
        </div>
    @empty
        @include('user.survey.partials.empty', [
            'message' => 'Form ini belum memiliki pertanyaan aktif.',
        ])
    @endforelse
</div>