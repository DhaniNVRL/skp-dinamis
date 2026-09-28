<div class="mx-5 mb-5 rounded-xl border border-violet-200 bg-violet-50 p-4">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div><h5 class="text-sm font-semibold text-violet-900">Pembanding Tahun</h5><p class="mt-1 text-xs {{ $question->comparison_enabled ? 'text-emerald-600' : 'text-gray-500' }}">{{ $question->comparison_enabled ? 'Aktif' : 'Belum diinput' }}</p></div>
        <button type="button" data-modal-open="comparisonModal"
            data-action="{{ route('question.comparison.update', $question->id) }}"
            data-enabled="{{ $question->comparison_enabled ? '1' : '0' }}"
            data-prompt="{{ $question->comparison_prompt }}"
            data-options="{{ json_encode($question->comparison_options ?? []) }}"
            class="inline-flex items-center gap-2 rounded-lg bg-violet-600 px-3 py-2 text-xs font-medium text-white hover:bg-violet-700"><i class="fa-solid fa-plus"></i> Input Pembanding</button>
    </div>
    @if ($question->comparison_enabled)
        <h5 class="mt-4 text-sm font-semibold text-violet-900">{{ $question->comparison_prompt }}</h5>
        <div class="mt-3 space-y-2">
            @foreach (($question->comparison_options ?? []) as $option)
                <label class="flex items-center gap-3 rounded-lg border border-violet-200 bg-white p-3 text-sm text-gray-700">
                    <input type="radio" disabled class="border-violet-300 text-violet-600">
                    {{ $option }}
                </label>
            @endforeach
        </div>
    @endif
</div>
