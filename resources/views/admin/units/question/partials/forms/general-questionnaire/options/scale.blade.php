@php
    $scaleMinimum = (int) ($question->scale_min ?? 1);
    $scaleMaximum = (int) ($question->scale_max ?? 10);
@endphp

<div class="space-y-3">
    <div class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-4">
        <span class="flex h-10 min-w-10 shrink-0 items-center justify-center rounded-lg bg-violet-100 px-2 text-sm font-semibold text-violet-700">
            {{ $question->no_header }}{{ $question->no }}
        </span>
        @include('admin.units.question.partials.forms.question-id-badge', compact('question'))

        <div class="min-w-0 flex-1">
            <h4 class="text-sm font-semibold text-gray-800">
                {{ $question->name }}
            </h4>
            <p class="mt-1 text-xs text-gray-500">Skala {{ $scaleMinimum }}–{{ $scaleMaximum }}</p>
        </div>

        <div class="flex shrink-0 items-center gap-2">
            @include(
                'admin.units.question.partials.forms.question-action',
                ['question' => $question, 'form' => $form]
            )
        </div>
    </div>

    <div class="rounded-xl border border-violet-200 bg-violet-50/50 p-4">
        <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-3">
            @if (filled($question->scale_left_label))
                <span class="text-sm font-medium text-gray-700">({{ $question->scale_left_label }})</span>
            @endif

            <div class="flex flex-wrap items-center justify-center gap-2">
                @foreach (range($scaleMinimum, $scaleMaximum) as $value)
                    <label class="cursor-default">
                        <input type="radio" disabled class="peer sr-only">
                        <span class="inline-flex h-9 min-w-9 items-center justify-center rounded-full border border-violet-300 bg-white px-2 text-sm font-medium text-violet-700">
                            {{ $value }}
                        </span>
                    </label>
                @endforeach
            </div>

            @if (filled($question->scale_right_label))
                <span class="text-sm font-medium text-gray-700">({{ $question->scale_right_label }})</span>
            @endif

            @if ($question->scale_include_zero && $scaleMinimum > 0)
                <span class="text-lg text-violet-300" aria-hidden="true">|</span>
                <label class="cursor-default">
                    <input type="radio" disabled class="peer sr-only">
                    <span class="inline-flex h-9 min-w-9 items-center justify-center rounded-full border border-violet-300 bg-white px-2 text-sm font-medium text-violet-700">0</span>
                </label>
            @endif
        </div>
    </div>

    @if (! empty($question->scale_child_values))
        <div class="rounded-xl border border-violet-200 bg-violet-50/30 p-4">
            <p class="mb-3 text-xs font-medium text-violet-700">
                Textarea muncul saat memilih skala: {{ implode(', ', $question->scale_child_values) }}
            </p>
            <textarea rows="2" disabled placeholder="Tulis alasan atau keterangan tambahan..." class="w-full rounded-lg border border-violet-200 bg-white px-3 py-2 text-sm"></textarea>
        </div>
    @endif
</div>
