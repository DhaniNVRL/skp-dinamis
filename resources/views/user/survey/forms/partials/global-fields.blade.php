<div class="space-y-5">
    @forelse ($questions as $question)
        @if ((int) $question->questiontype_id === 1)
            <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 font-semibold text-indigo-900">
                {{ $question->name }}
            </div>
        @else
            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <div class="mb-4 flex items-start gap-3">
                    @include('user.survey.partials.question-number', compact('question'))
                    <label for="answer-{{ $question->id }}" class="font-semibold text-gray-800">{{ $question->name }}</label>
                </div>
                <textarea id="answer-{{ $question->id }}" name="answers[{{ $question->id }}][value]" rows="3" required class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" placeholder="Tulis jawaban...">{{ old("answers.{$question->id}.value", data_get($answerMap, $question->id.'.0.0.value')) }}</textarea>
                <p data-field-error class="mt-2 hidden text-sm text-red-600">Pertanyaan ini wajib diisi.</p>
            </div>
        @endif
    @empty
        @include('user.survey.partials.empty')
    @endforelse
</div>
