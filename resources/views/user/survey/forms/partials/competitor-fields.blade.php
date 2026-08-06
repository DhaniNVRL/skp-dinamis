<div class="space-y-6">
    @forelse ($competitors as $competitor)
        <section class="overflow-hidden rounded-xl border border-emerald-200 bg-emerald-50/40">
            <header class="border-b border-emerald-200 bg-emerald-50 px-5 py-4">
                <div class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Kompetitor</div>
                <h2 class="font-bold text-gray-900">{{ $competitor->name }}</h2>
            </header>
            <div class="space-y-4 p-5">
                @foreach ($questions->where('questiontype_id', '!=', 1) as $question)
                    <div class="rounded-xl border border-gray-200 bg-white p-5">
                        <div class="mb-4 flex items-start gap-3">
                            @include('user.survey.partials.question-number', compact('question'))
                            <label for="answer-{{ $question->id }}-competitor-{{ $competitor->id }}" class="font-semibold text-gray-800">{{ $question->name }}</label>
                        </div>
                        <textarea id="answer-{{ $question->id }}-competitor-{{ $competitor->id }}" name="answers[{{ $question->id }}][{{ $competitor->id }}][value]" rows="3" required class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" placeholder="Tulis jawaban...">{{ old("answers.{$question->id}.{$competitor->id}.value", data_get($answerMap, $question->id.'.0.'.$competitor->id.'.value')) }}</textarea>
                        <p data-field-error class="mt-2 hidden text-sm text-red-600">Pertanyaan ini wajib diisi.</p>
                    </div>
                @endforeach
            </div>
        </section>
    @empty
        @include('user.survey.partials.empty', ['message' => 'Data kompetitor belum tersedia.'])
    @endforelse
</div>
