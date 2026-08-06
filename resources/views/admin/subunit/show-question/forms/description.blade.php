<div class="space-y-5">
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
                <div class="flex items-start gap-3">
                    @include(
                        'admin.subunit.show-question.forms.partials.question-number',
                        compact('question')
                    )

                    <h3 class="font-semibold text-gray-800">
                        {{ $question->name }}
                    </h3>
                </div>
            </div>
        @endforeach

        @php
            $tableQuestions = $group
                ->filter(function ($question) {
                    return (int) (
                        $question->questiontype_id
                        ?? $question->id_questiontypes
                        ?? 0
                    ) === 2;
                })
                ->sortBy('no');
        @endphp

        @if ($tableQuestions->isNotEmpty())
            <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="min-w-72 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                                Pertanyaan
                            </th>

                            @foreach ($competitors as $competitor)
                                <th class="min-w-64 px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600">
                                    {{ $competitor->name }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @foreach ($tableQuestions as $question)
                            <tr>
                                <td class="px-4 py-4 align-top">
                                    <div class="flex items-start gap-3">
                                        @include(
                                            'admin.subunit.show-question.forms.partials.question-number',
                                            compact('question')
                                        )

                                        <span class="text-sm font-medium text-gray-700">
                                            {{ $question->name }}
                                        </span>
                                    </div>
                                </td>

                                @foreach ($competitors as $competitor)
                                    <td class="px-4 py-4 text-center align-middle">
                                        @include(
                                            'admin.subunit.show-question.forms.partials.scale',
                                            [
                                                'question' => $question,
                                                'maximum' => 5,
                                                'name' => "competitor_{$question->id}_{$competitor->id}",
                                            ]
                                        )
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @empty
        @include('admin.subunit.show-question.partials.empty')
    @endforelse
</div>