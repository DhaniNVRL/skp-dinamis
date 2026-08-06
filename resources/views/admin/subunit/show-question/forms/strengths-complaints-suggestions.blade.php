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
                    <div class="mb-5 flex items-start gap-3">
                        @include(
                            'admin.subunit.show-question.forms.partials.question-number',
                            compact('question')
                        )

                        <div class="font-medium text-gray-800">
                            {{ $question->name }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
                        <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                            <label class="mb-2 block font-semibold text-green-700">
                                Keunggulan
                            </label>

                            <textarea
                                rows="4"
                                class="w-full rounded-lg border border-green-200 bg-white px-3 py-2 text-sm"
                                placeholder="Tuliskan keunggulan..."
                            ></textarea>
                        </div>

                        <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                            <label class="mb-2 block font-semibold text-red-700">
                                Keluhan
                            </label>

                            <textarea
                                rows="4"
                                class="w-full rounded-lg border border-red-200 bg-white px-3 py-2 text-sm"
                                placeholder="Tuliskan keluhan..."
                            ></textarea>
                        </div>

                        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                            <label class="mb-2 block font-semibold text-blue-700">
                                Saran
                            </label>

                            <textarea
                                rows="4"
                                class="w-full rounded-lg border border-blue-200 bg-white px-3 py-2 text-sm"
                                placeholder="Tuliskan saran..."
                            ></textarea>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @empty
        @include('admin.subunit.show-question.partials.empty')
    @endforelse
</div>