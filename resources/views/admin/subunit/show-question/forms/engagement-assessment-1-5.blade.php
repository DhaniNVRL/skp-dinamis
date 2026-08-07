<div class="space-y-5">
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
            @endphp

            {{-- JUDUL --}}
            @if ($questionTypeId === 1)
                <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-5">
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
            @else
                {{-- PERTANYAAN --}}
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="mb-6 flex items-start gap-3">
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
                                Penilaian Keterikatan Skala 1–5
                            </p>
                        </div>
                    </div>

                    {{-- SKALA TENGAH --}}
                    <div class="flex w-full justify-center">
                        <div class="w-fit">
                            @include(
                                'admin.subunit.show-question.forms.partials.scale',
                                [
                                    'question' => $question,
                                    'maximum' => 5,
                                    'includeZero' => false,
                                    'name' => "engagement_{$question->id}",
                                    'leftLabel' => 'Sangat tidak setuju',
                                    'rightLabel' => 'Sangat setuju',
                                ]
                            )
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @empty
        @include(
            'admin.subunit.show-question.forms.partials.empty'
        )
    @endforelse
</div>
