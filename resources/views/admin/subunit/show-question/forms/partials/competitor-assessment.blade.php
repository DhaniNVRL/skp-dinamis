<div class="space-y-5" data-competitor-assessment-show>
    @forelse ($questions->groupBy('no_header') as $header => $group)
        @foreach (
            $group->filter(function ($question) {
                return (int) (
                    $question->questiontype_id
                    ?? $question->id_questiontypes
                    ?? 0
                ) === 1;
            })->sortBy('no')
            as $question
        )
            <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-4">
                <h3 class="font-semibold text-gray-800">
                    {{ $question->name }}
                </h3>
            </div>
        @endforeach

        @php
            $assessmentQuestions = $group
                ->filter(function ($question) {
                    return (int) (
                        $question->questiontype_id
                        ?? $question->id_questiontypes
                        ?? 0
                    ) === 2;
                })
                ->sortBy('no');
        @endphp

        @foreach ($assessmentQuestions as $question)
            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <header class="border-b border-gray-200 bg-gray-50 px-5 py-4">
                    <div class="flex items-start gap-3">
                        @include(
                            'admin.subunit.show-question.forms.partials.question-number',
                            compact('question')
                        )

                        <h3 class="font-semibold leading-6 text-gray-800">
                            {{ $question->name }}
                        </h3>
                    </div>
                </header>

                <div class="divide-y divide-gray-200" data-competitor-list>
                    @forelse ($competitors as $competitor)
                        <div
                            data-competitor-row
                            class="grid gap-4 px-5 py-4 transition hover:bg-gray-50 md:grid-cols-[minmax(12rem,18rem)_1fr] md:items-center"
                        >
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-violet-100 text-violet-600">
                                    <i class="fa-solid fa-building text-xs"></i>
                                </span>

                                <span class="break-words text-sm font-semibold text-gray-700">
                                    {{ $competitor->name }}
                                </span>
                            </div>

                            <div class="min-w-0">
                                @include(
                                    'admin.subunit.show-question.forms.partials.scale',
                                    [
                                        'question' => $question,
                                        'maximum' => $maximum,
                                        'includeZero' => true,
                                        'name' => "competitor_{$question->id}_{$competitor->id}",
                                        'zeroLabel' => null,
                                    ]
                                )
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center text-sm text-gray-500">
                            Belum ada kompetitor.
                        </div>
                    @endforelse
                </div>
            </section>
        @endforeach
    @empty
        @include('admin.subunit.show-question.forms.partials.empty')
    @endforelse
</div>
