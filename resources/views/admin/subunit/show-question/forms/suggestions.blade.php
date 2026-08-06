<div class="space-y-5">
    @forelse ($questions->groupBy('no_header') as $header => $group)
        @foreach ($group->sortBy('no') as $question)
            @php
                $questionTypeId = (int) (
                    $question->questiontype_id
                    ?? $question->id_questiontypes
                    ?? 0
                );
            @endphp

            @if ($questionTypeId === 1)
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
            @else
                <div class="rounded-lg border border-gray-200 bg-white p-5">
                    <div class="mb-4 flex items-start gap-3">
                        @include(
                            'admin.subunit.show-question.forms.partials.question-number',
                            compact('question')
                        )

                        <div class="font-medium text-gray-800">
                            {{ $question->name }}
                        </div>
                    </div>

                    <textarea
                        rows="4"
                        class="w-full rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm"
                        placeholder="Tuliskan saran Anda..."
                    ></textarea>
                </div>
            @endif
        @endforeach
    @empty
        @include('admin.subunit.show-question.partials.empty')
    @endforelse
</div>