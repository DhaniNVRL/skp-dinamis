@php
    $scopeId = isset($subunit)
        ? $subunit->id
        : 'global';

    /*
     * Customer Assessment 1-5.
     */
    $scaleMaximum = 5;

    /*
     * Alasan hanya muncul jika nilai Kinerja 1-3.
     */
    $reasonMaximumScore = 3;
@endphp

<div
    data-customer-assessment
    class="space-y-5"
>
    @forelse (
        $questions->groupBy('no_header')
        as $noHeader => $group
    )
        @foreach ($group->sortBy('no') as $question)
            @php
                $questionTypeId = (int) (
                    $question->questiontype_id
                    ?? $question->id_questiontypes
                    ?? 0
                );

                $questionScope =
                    $question->id
                    . '_'
                    . $scopeId;
            @endphp

            {{-- TYPE 1: JUDUL --}}
            @if ($questionTypeId === 1)
                <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                    <div class="flex items-start gap-3">
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
                </div>

            {{-- TYPE 2: KEPENTINGAN DAN KINERJA --}}
            @elseif ($questionTypeId === 2)
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="mb-5 flex items-start gap-3">
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
                                Penilaian Kepentingan dan Kinerja
                            </p>
                        </div>
                    </div>

                    @include(
                        'admin.subunit.show-question.forms.partials.importance-performance',
                        [
                            'question' => $question,
                            'scopeId' => $scopeId,
                            'scaleMaximum' => $scaleMaximum,
                        ]
                    )
                </div>

            {{-- TYPE 3: ALASAN TEXTAREA --}}
            @elseif ($questionTypeId === 3)
                <div
                    data-customer-question
                    data-reason-maximum-score="3"
                    class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"
                >
                    <div class="mb-5 flex items-start gap-3">
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
                                Penilaian Kepentingan dan Kinerja dengan alasan
                            </p>
                        </div>
                    </div>

                    @include(
                        'admin.subunit.show-question.forms.partials.importance-performance',
                        [
                            'question' => $question,
                            'scopeId' => $scopeId,
                            'scaleMaximum' => $scaleMaximum,
                        ]
                    )

                    <div
                        data-customer-reason-panel
                        class="mt-5 hidden rounded-xl border border-gray-200 bg-gray-50 p-4"
                    >
                        <label
                            for="reason_{{ $questionScope }}"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Alasan
                        </label>

                        <p class="mb-3 text-xs text-gray-500">
                            Alasan muncul karena nilai Kinerja berada pada rentang 1–3.
                        </p>

                        <textarea
                            id="reason_{{ $questionScope }}"
                            name="reason[{{ $question->id }}][{{ $scopeId }}]"
                            rows="3"
                            data-customer-reason-input
                            disabled
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                            placeholder="Tuliskan alasan penilaian Anda..."
                        ></textarea>
                    </div>
                </div>

            {{-- TYPE 4: PILIHAN ALASAN --}}
            @elseif ($questionTypeId === 4)
                <div
                    data-customer-question
                    data-reason-maximum-score="3"
                    class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"
                >
                    <div class="mb-5 flex items-start gap-3">
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
                                Penilaian Kepentingan dan Kinerja dengan pilihan alasan
                            </p>
                        </div>
                    </div>

                    @include(
                        'admin.subunit.show-question.forms.partials.importance-performance',
                        [
                            'question' => $question,
                            'scopeId' => $scopeId,
                            'scaleMaximum' => $scaleMaximum,
                        ]
                    )

                    <div
                        data-customer-reason-panel
                        class="mt-5 hidden rounded-xl border border-gray-200 bg-gray-50 p-4"
                    >
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-800">
                                Pilihan Alasan
                            </h4>

                            <p class="mt-1 text-xs text-gray-500">
                                Pilih satu atau beberapa alasan yang sesuai.
                            </p>
                        </div>

                        <div class="space-y-3">
                            @forelse ($question->options as $option)
                                @php
                                    $hasChild =
                                        (int) $option->has_child === 1;

                                    $optionId =
                                        'reason_'
                                        . $question->id
                                        . '_'
                                        . $scopeId
                                        . '_'
                                        . $option->id;
                                @endphp

                                <div
                                    data-reason-option
                                    class="rounded-lg border border-gray-200 bg-white p-4"
                                >
                                    <div class="flex items-start gap-3">
                                        <input
                                            id="{{ $optionId }}"
                                            type="checkbox"
                                            name="reason_options[{{ $question->id }}][{{ $scopeId }}][]"
                                            value="{{ $option->id }}"
                                            data-reason-checkbox
                                            data-customer-reason-input
                                            disabled
                                            class="mt-1 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        >

                                        <label
                                            for="{{ $optionId }}"
                                            class="cursor-pointer text-sm font-medium text-gray-700"
                                        >
                                            {{ $option->answer_text }}
                                        </label>
                                    </div>

                                    @if ($hasChild)
                                        <div
                                            data-reason-child
                                            class="mt-3 hidden pl-7"
                                        >
                                            @if (!empty($option->child_label))
                                                <label
                                                    for="child_{{ $optionId }}"
                                                    class="mb-2 block text-xs font-medium text-gray-600"
                                                >
                                                    {{ $option->child_label }}
                                                </label>
                                            @endif

                                            <textarea
                                                id="child_{{ $optionId }}"
                                                name="reason_children[{{ $question->id }}][{{ $scopeId }}][{{ $option->id }}]"
                                                rows="2"
                                                data-customer-reason-child-input
                                                disabled
                                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                                                placeholder="{{ $option->answer_text2 ?: 'Tuliskan keterangan tambahan...' }}"
                                            ></textarea>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
                                    Pilihan alasan belum tersedia.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            {{-- TYPE 5: SATU INDIKATOR --}}
            @elseif ($questionTypeId === 5)
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="mb-5 flex items-start gap-3">
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
                                Penilaian Satu Indikator
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-center rounded-xl border border-indigo-200 bg-indigo-50 p-5">
                        @include(
                            'admin.subunit.show-question.forms.partials.scale',
                            [
                                'question' => $question,
                                'maximum' => $scaleMaximum,
                                'includeZero' => true,
                                'name' => "indicator_{$questionScope}",
                                'leftLabel' => null,
                                'rightLabel' => null,
                                'zeroLabel' => null,
                            ]
                        )
                    </div>
                </div>

            {{-- TYPE 6: TEXTAREA --}}
            @elseif ($questionTypeId === 6)
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
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
                                Jawaban Teks
                            </p>
                        </div>
                    </div>

                    <textarea
                        name="text_answer[{{ $question->id }}][{{ $scopeId }}]"
                        rows="4"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                        placeholder="Tulis jawaban Anda..."
                    ></textarea>
                </div>

            @else
                <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-yellow-700">
                    Question Type {{ $questionTypeId }} belum didukung:
                    {{ $question->name }}
                </div>
            @endif
        @endforeach
    @empty
        @include(
            'admin.subunit.show-question.forms.partials.empty'
        )
    @endforelse
</div>
