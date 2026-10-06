@php
    $scaleMinimum = (int) ($question->scale_min ?? 1);
    $scaleMaximum = (int) ($question->scale_max ?? 10);
@endphp

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
    </div>
</div>
