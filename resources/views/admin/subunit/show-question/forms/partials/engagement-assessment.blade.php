@php
    $scaleValues = range(1, (int) $maximumScale);
@endphp

<div class="space-y-5">
    @forelse ($questions->groupBy('no_header') as $group)
        @foreach ($group as $question)
            @php
                $questionTypeId = (int) (
                    $question->questiontype_id
                    ?? $question->id_questiontypes
                    ?? 0
                );
            @endphp

            @if ($questionTypeId === 1)
                <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-5">
                    <div class="flex items-start gap-3">
                        @include(
                            'admin.subunit.show-question.forms.partials.question-number',
                            ['question' => $question]
                        )
                        <h3 class="font-semibold text-gray-800">{{ $question->name }}</h3>
                    </div>
                </div>
            @else
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <div class="flex items-start gap-3">
                            @include(
                                'admin.subunit.show-question.forms.partials.question-number',
                                ['question' => $question]
                            )
                            <div>
                                <h3 class="font-semibold leading-relaxed text-gray-900">{{ $question->name }}</h3>
                                <p class="mt-1 text-xs text-gray-500">
                                    Penilaian Keterikatan Skala 1–{{ $maximumScale }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5">
                        <div class="rounded-xl border border-violet-200 bg-violet-50/50 p-4">
                            <div class="flex flex-wrap items-center justify-center gap-3">
                                @foreach ($scaleValues as $value)
                                    <label
                                        for="preview-engagement-{{ $question->id }}-{{ $value }}"
                                        class="cursor-pointer"
                                    >
                                        <input
                                            id="preview-engagement-{{ $question->id }}-{{ $value }}"
                                            type="radio"
                                            name="preview_engagement_{{ $question->id }}"
                                            value="{{ $value }}"
                                            class="peer sr-only"
                                        >

                                        <span
                                            class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-violet-300 bg-white text-sm font-semibold text-violet-700 transition hover:border-violet-500 hover:bg-violet-100 peer-checked:border-violet-600 peer-checked:bg-violet-600 peer-checked:text-white peer-focus:ring-2 peer-focus:ring-violet-200"
                                        >
                                            {{ $value }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @empty
        @include('admin.subunit.show-question.forms.partials.empty')
    @endforelse
</div>
