<div class="space-y-6">
    @forelse ($subunits as $subunit)
        @php($subunitQuestions = $questions->filter(fn ($question) => in_array($subunit->id, $activeMapSubUnit[$form->id.'-'.$question->id] ?? [], true)))
        @if ($subunitQuestions->isNotEmpty())
            <section class="overflow-hidden rounded-xl border border-blue-200 bg-blue-50/40">
                <header class="border-b border-blue-200 bg-blue-50 px-5 py-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-blue-600">Sub Unit</div>
                    <h2 class="font-bold text-gray-900">{{ $subunit->name }}</h2>
                </header>
                <div class="space-y-4 p-5">
                    @foreach ($subunitQuestions as $question)
                        @if ((int) $question->questiontype_id !== 1)
                            <div class="rounded-xl border border-gray-200 bg-white p-5">
                                <div class="mb-4 flex items-start gap-3">
                                    @include('user.survey.partials.question-number', compact('question'))
                                    <label for="answer-{{ $question->id }}-{{ $subunit->id }}" class="font-semibold text-gray-800">{{ $question->name }}</label>
                                </div>
                                <textarea id="answer-{{ $question->id }}-{{ $subunit->id }}" name="answers[{{ $question->id }}][{{ $subunit->id }}][value]" rows="3" required class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" placeholder="Tulis jawaban...">{{ old("answers.{$question->id}.{$subunit->id}.value", data_get($answerMap, $question->id.'.'.$subunit->id.'.0.value')) }}</textarea>
                                <p data-field-error class="mt-2 hidden text-sm text-red-600">Pertanyaan ini wajib diisi.</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif
    @empty
        @include('user.survey.partials.empty', ['message' => 'Unit Anda belum memiliki Sub Unit.'])
    @endforelse
</div>
