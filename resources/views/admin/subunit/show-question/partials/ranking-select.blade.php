@php
    $rankingName = $name
        ?? 'ranking_' . $question->id;

    $rankingLabel = $label
        ?? 'Ranking';

    $rankingIndex = (int) (
        $rank ?? 1
    );
@endphp

<div
    data-ranking-block
    class="space-y-2"
>
    <label
        for="{{ $rankingName }}"
        class="block text-sm font-medium text-gray-600"
    >
        {{ $rankingLabel }}
    </label>

    <select
        id="{{ $rankingName }}"
        name="{{ $rankingName }}"
        data-ranking-select
        data-question-id="{{ $question->id }}"
        data-ranking-index="{{ $rankingIndex }}"
        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
    >
        <option
            value=""
            data-has-child="0"
        >
            -- Pilih jawaban --
        </option>

        @foreach ($question->options as $option)
            <option
                value="{{ $option->id }}"
                data-has-child="{{ (int) $option->has_child }}"
                data-answer-text2="{{ $option->answer_text2 ?? '' }}"
            >
                {{ $option->answer_text }}
            </option>
        @endforeach
    </select>

    {{-- CHILD TEXTAREA --}}
    <div
        data-ranking-child
        class="hidden rounded-lg border border-indigo-200 bg-indigo-50 p-3"
    >
        <label
            for="{{ $rankingName }}_child"
            class="mb-2 block text-xs font-medium text-gray-600"
        >
            Jawaban tambahan
        </label>

        <textarea
            id="{{ $rankingName }}_child"
            name="{{ $rankingName }}_child"
            rows="3"
            data-ranking-child-input
            disabled
            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
            placeholder="Jawaban tambahan..."
        ></textarea>
    </div>
</div>
