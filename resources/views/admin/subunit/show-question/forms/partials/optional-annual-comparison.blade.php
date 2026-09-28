@if ($question->comparison_enabled)
    <div class="rounded-xl border border-violet-200 bg-violet-50 p-5">
        <h4 class="font-semibold text-gray-900">{{ $question->comparison_prompt }}</h4>
        <p class="mt-1 text-xs text-gray-500">Pilih satu jawaban yang paling sesuai.</p>
        <div class="mt-4 space-y-3">
            @foreach (($question->comparison_options ?? []) as $option)
                <label class="flex items-center gap-3 rounded-lg border border-violet-200 bg-white p-4 text-sm font-medium text-gray-800">
                    <input type="radio" name="comparison_{{ $question->id }}_{{ $scopeId }}" value="{{ $option }}" class="h-4 w-4 border-gray-300 text-violet-600">
                    {{ $option }}
                </label>
            @endforeach
        </div>
    </div>
@endif
