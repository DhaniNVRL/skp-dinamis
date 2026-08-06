@php
    $scopeId = isset($subunit)
        ? $subunit->id
        : 'global';
@endphp

<div
    data-general-questionnaire
    class="space-y-5"
>
    @forelse ($questions->sortBy('no') as $question)
        @php
            $questionTypeId = (int) (
                $question->questiontype_id
                ?? $question->id_questiontypes
                ?? 0
            );

            $questionContainerId =
                'general_question_'
                . $question->id
                . '_'
                . $scopeId;
        @endphp

        {{-- TYPE 1: JUDUL --}}
        @if ($questionTypeId === 1)
            <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-5">
                <div class="flex items-start gap-3">
                    @include(
                        'admin.subunit.show-question.forms.partials.question-number',
                        [
                            'question' => $question,
                        ]
                    )

                    <div>
                        <h3 class="font-semibold text-gray-800">
                            {{ $question->name }}
                        </h3>

                        <p class="mt-1 text-xs text-gray-500">
                            Judul Pertanyaan
                        </p>
                    </div>
                </div>
            </div>

        {{-- TYPE 2: TEXTAREA --}}
        @elseif ($questionTypeId === 2)
            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <div class="mb-4 flex items-start gap-3">
                    @include(
                        'admin.subunit.show-question.forms.partials.question-number',
                        [
                            'question' => $question,
                        ]
                    )

                    <h3 class="font-semibold text-gray-800">
                        {{ $question->name }}
                    </h3>
                </div>

                <textarea
                    name="general_answers[{{ $question->id }}][{{ $scopeId }}]"
                    rows="4"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                    placeholder="Tulis jawaban Anda..."
                ></textarea>
            </div>

        {{-- TYPE 3: RADIO --}}
        @elseif ($questionTypeId === 3)
            <div
                id="{{ $questionContainerId }}"
                data-general-question
                data-general-question-type="radio"
                class="rounded-xl border border-gray-200 bg-white p-5"
            >
                <div class="mb-4 flex items-start gap-3">
                    @include(
                        'admin.subunit.show-question.forms.partials.question-number',
                        [
                            'question' => $question,
                        ]
                    )

                    <h3 class="font-semibold text-gray-800">
                        {{ $question->name }}
                    </h3>
                </div>

                <div class="space-y-3">
                    @forelse ($question->options as $option)
                        @php
                            $hasChild =
                                (int) $option->has_child === 1;

                            $optionInputId =
                                'general_radio_'
                                . $question->id
                                . '_'
                                . $scopeId
                                . '_'
                                . $option->id;
                        @endphp

                        <div
                            data-general-option
                            class="rounded-lg border border-gray-200 bg-white p-4 transition hover:bg-gray-50"
                        >
                            <div class="flex items-start gap-3">
                                <input
                                    id="{{ $optionInputId }}"
                                    type="radio"
                                    name="general_answers[{{ $question->id }}][{{ $scopeId }}]"
                                    value="{{ $option->id }}"
                                    data-general-option-input
                                    data-has-child="{{ $hasChild ? '1' : '0' }}"
                                    class="mt-1 h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                >

                                <label
                                    for="{{ $optionInputId }}"
                                    class="min-w-0 flex-1 cursor-pointer text-sm font-medium text-gray-700"
                                >
                                    {{ $option->answer_text }}
                                </label>
                            </div>

                            @if ($hasChild)
                                <div
                                    data-general-child
                                    class="mt-3 hidden pl-7"
                                >
                                    @if (!empty($option->child_label))
                                        <label
                                            for="child_{{ $optionInputId }}"
                                            class="mb-2 block text-xs font-medium text-gray-600"
                                        >
                                            {{ $option->child_label }}
                                        </label>
                                    @endif

                                    <textarea
                                        id="child_{{ $optionInputId }}"
                                        name="general_child_answers[{{ $question->id }}][{{ $scopeId }}][{{ $option->id }}]"
                                        rows="3"
                                        disabled
                                        data-general-child-input
                                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                                        placeholder="{{ $option->child_placeholder ?? 'Jawaban tambahan...' }}"
                                    ></textarea>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500">
                            Pilihan jawaban belum tersedia.
                        </div>
                    @endforelse
                </div>
            </div>

        {{-- TYPE 4: CHECKBOX --}}
        @elseif ($questionTypeId === 4)
            <div
                id="{{ $questionContainerId }}"
                data-general-question
                data-general-question-type="checkbox"
                class="rounded-xl border border-gray-200 bg-white p-5"
            >
                <div class="mb-4 flex items-start gap-3">
                    @include(
                        'admin.subunit.show-question.forms.partials.question-number',
                        [
                            'question' => $question,
                        ]
                    )

                    <div>
                        <h3 class="font-semibold text-gray-800">
                            {{ $question->name }}
                        </h3>

                        <p class="mt-1 text-xs text-gray-500">
                            Anda dapat memilih lebih dari satu jawaban.
                        </p>
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse ($question->options as $option)
                        @php
                            $hasChild =
                                (int) $option->has_child === 1;

                            $optionInputId =
                                'general_checkbox_'
                                . $question->id
                                . '_'
                                . $scopeId
                                . '_'
                                . $option->id;
                        @endphp

                        <div
                            data-general-option
                            class="rounded-lg border border-gray-200 bg-white p-4 transition hover:bg-gray-50"
                        >
                            <div class="flex items-start gap-3">
                                <input
                                    id="{{ $optionInputId }}"
                                    type="checkbox"
                                    name="general_answers[{{ $question->id }}][{{ $scopeId }}][]"
                                    value="{{ $option->id }}"
                                    data-general-option-input
                                    data-has-child="{{ $hasChild ? '1' : '0' }}"
                                    class="mt-1 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                >

                                <label
                                    for="{{ $optionInputId }}"
                                    class="min-w-0 flex-1 cursor-pointer text-sm font-medium text-gray-700"
                                >
                                    {{ $option->answer_text }}
                                </label>
                            </div>

                            @if ($hasChild)
                                <div
                                    data-general-child
                                    class="mt-3 hidden pl-7"
                                >
                                    @if (!empty($option->child_label))
                                        <label
                                            for="child_{{ $optionInputId }}"
                                            class="mb-2 block text-xs font-medium text-gray-600"
                                        >
                                            {{ $option->child_label }}
                                        </label>
                                    @endif

                                    <textarea
                                        id="child_{{ $optionInputId }}"
                                        name="general_child_answers[{{ $question->id }}][{{ $scopeId }}][{{ $option->id }}]"
                                        rows="3"
                                        disabled
                                        data-general-child-input
                                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                                        placeholder="{{ $option->child_placeholder ?? 'Jawaban tambahan...' }}"
                                    ></textarea>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500">
                            Pilihan jawaban belum tersedia.
                        </div>
                    @endforelse
                </div>
            </div>

        {{-- TYPE LAIN --}}
        @else
            <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-triangle-exclamation mt-1 text-yellow-500"></i>

                    <div>
                        <div class="font-medium text-gray-800">
                            {{ $question->name }}
                        </div>

                        <p class="mt-1 text-sm text-yellow-700">
                            Question Type {{ $questionTypeId }} belum didukung.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    @empty
        @include(
            'admin.subunit.show-question.forms.partials.empty'
        )
    @endforelse
</div>