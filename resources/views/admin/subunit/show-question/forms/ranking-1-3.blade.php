<div class="space-y-5">
    @forelse (
        $questions->groupBy('no_header')
        as $header => $group
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
                <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-4">
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
            @else
                {{-- PERTANYAAN RANKING --}}
                <div
                    data-ranking-question
                    data-question-id="{{ $question->id }}"
                    class="rounded-lg border border-gray-200 bg-white p-5"
                >
                    <div class="mb-5 flex items-start gap-3">
                        @include(
                            'admin.subunit.show-question.forms.partials.question-number',
                            [
                                'question' => $question,
                            ]
                        )

                        <div>
                            <h3 class="font-medium text-gray-800">
                                {{ $question->name }}
                            </h3>

                            <p class="mt-1 text-xs text-gray-500">
                                Pilih urutan ranking 1 sampai 3.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        @for ($rank = 1; $rank <= 3; $rank++)
                            @include(
                                'admin.subunit.show-question.partials.ranking-select',
                                [
                                    'question' => $question,
                                    'label' => 'Ranking ' . $rank,
                                    'name' => "ranking_{$question->id}_{$rank}",
                                    'rank' => $rank,
                                ]
                            )
                        @endfor
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