@php
    $scaleName = $name
        ?? 'preview_' . $question->id;

    $scaleMaximum = (int) (
        $maximum ?? 5
    );

    /*
     * Tampilkan nilai 0 setelah nilai terbesar.
     */
    $includeZero = (bool) (
        $includeZero ?? false
    );

    $leftLabel = $leftLabel ?? null;
    $rightLabel = $rightLabel ?? null;
    $zeroLabel = $zeroLabel
        ?? '';
@endphp

<div class="space-y-3">
    <div class="flex flex-wrap items-center gap-y-3">
        {{-- NILAI UTAMA --}}
        <div class="flex flex-wrap items-center gap-2">
            @for ($score = 1; $score <= $scaleMaximum; $score++)
                <label class="cursor-pointer">
                    <input
                        type="radio"
                        name="{{ $scaleName }}"
                        value="{{ $score }}"
                        class="peer sr-only"
                    >

                    <span class="flex h-9 w-9 items-center justify-center rounded-full border border-gray-300 bg-white text-sm text-gray-600 transition hover:border-indigo-400 hover:bg-indigo-50 peer-checked:border-indigo-600 peer-checked:bg-indigo-600 peer-checked:text-white">
                        {{ $score }}
                    </span>
                </label>
            @endfor
        </div>

        {{-- NILAI 0 --}}
        @if ($includeZero)
            <div class="ml-6 border-l border-gray-300 pl-6">
                <label class="group flex cursor-pointer items-center gap-2">
                    <input
                        type="radio"
                        name="{{ $scaleName }}"
                        value="0"
                        class="peer sr-only"
                    >

                    <span class="flex h-9 w-9 items-center justify-center rounded-full border border-amber-300 bg-amber-50 text-sm font-medium text-amber-700 transition hover:border-amber-500 hover:bg-amber-100 peer-checked:border-amber-600 peer-checked:bg-amber-500 peer-checked:text-white">
                        0
                    </span>

                    @if ($zeroLabel)
                        <span class="hidden text-xs text-gray-500 lg:inline">
                            {{ $zeroLabel }}
                        </span>
                    @endif
                </label>
            </div>
        @endif
    </div>

    @if ($leftLabel || $rightLabel)
        <div class="flex max-w-md justify-between text-xs text-gray-400">
            <span>
                {{ $leftLabel }}
            </span>

            <span>
                {{ $rightLabel }}
            </span>
        </div>
    @endif
</div>