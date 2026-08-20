@php
    $assessmentQuestions = collect($questions ?? [])->reject(fn ($question) =>
        (int) ($question->questiontype_id ?? 0) === 1
    )->values();
    $savedCompetitors = collect($respondentCompetitors ?? [])->values();
    $oldCompetitors = old('respondent_competitors');
    $initialCompetitors = is_array($oldCompetitors)
        ? collect($oldCompetitors)->values()
        : $savedCompetitors->map(fn ($item) => ['id' => $item->id, 'name' => $item->name])->values();
    if ($initialCompetitors->isEmpty()) {
        $initialCompetitors = collect([['id' => null, 'name' => '']]);
    }
@endphp

<div id="respondentCompetitorForm" class="space-y-6" data-next-index="{{ $initialCompetitors->count() }}">
    <section class="rounded-xl border border-blue-200 bg-blue-50 p-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h3 class="font-semibold text-gray-900">Kompetitor yang akan dinilai</h3>
                <p class="mt-1 text-sm text-gray-600">Masukkan nama kompetitor. Setiap responden dapat menentukan kompetitornya sendiri.</p>
            </div>
            <button type="button" data-add-respondent-competitor class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                <i class="fa-solid fa-plus"></i> Tambah Kompetitor
            </button>
        </div>
        <div data-competitor-name-list class="mt-5 grid gap-3 md:grid-cols-2">
            @foreach ($initialCompetitors as $index => $competitor)
                <div data-competitor-name-row="{{ $index }}" class="flex items-center gap-2 rounded-lg border border-blue-200 bg-white p-3">
                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-100 font-semibold text-blue-700">{{ $index + 1 }}</span>
                    <input type="hidden" name="respondent_competitors[{{ $index }}][id]" value="{{ data_get($competitor, 'id') }}">
                    <input required maxlength="150" name="respondent_competitors[{{ $index }}][name]" value="{{ data_get($competitor, 'name') }}" placeholder="Nama kompetitor" class="min-w-0 flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-200">
                    <button type="button" data-remove-respondent-competitor="{{ $index }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100" title="Hapus kompetitor"><i class="fa-solid fa-trash"></i></button>
                </div>
            @endforeach
        </div>
        <p class="mt-3 text-xs text-gray-500">Minimal 1 dan maksimal 10 kompetitor. Nama tidak boleh sama.</p>
    </section>

    <div class="space-y-5">
        @foreach ($assessmentQuestions as $question)
            @php
                $number = trim(($question->no_header ?? '').($question->no ?? ''));
            @endphp
            <section data-dynamic-question="{{ $question->id }}" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <header class="flex items-start gap-3 border-b border-gray-200 bg-gray-50 px-5 py-4">
                    @if ($number)<span class="inline-flex min-w-10 justify-center rounded-lg bg-blue-100 px-2.5 py-1 text-sm font-semibold text-blue-700">{{ $number }}</span>@endif
                    <div><h3 class="font-semibold text-gray-800">{{ $question->name }}</h3><p class="mt-1 text-xs text-gray-500">Berikan nilai 1–7, atau 0 jika tidak dapat menilai.</p></div>
                </header>
                <div data-dynamic-rating-list class="divide-y divide-gray-200">
                    @foreach ($initialCompetitors as $index => $competitor)
                        @php
                            $savedId = data_get($competitor, 'id');
                            $stored = old("answers.{$question->id}.{$index}.value", $savedId ? data_get($answerMap, "{$question->id}.0.{$savedId}.value") : null);
                        @endphp
                        <div data-rating-row="{{ $index }}" class="grid gap-4 px-5 py-4 md:grid-cols-[minmax(12rem,18rem)_1fr] md:items-center">
                            <div class="flex items-center gap-3"><span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600"><i class="fa-solid fa-building"></i></span><span data-competitor-display="{{ $index }}" class="font-semibold text-gray-700">{{ data_get($competitor, 'name') ?: 'Kompetitor '.($index + 1) }}</span></div>
                            <div class="flex flex-wrap items-center justify-center gap-2" data-option-group>
                                @foreach ([1,2,3,4,5,6,7,0] as $value)
                                    @if ($value === 0)<span class="mx-2 h-10 border-l border-gray-300"></span>@endif
                                    <label class="cursor-pointer"><input required type="radio" name="answers[{{ $question->id }}][{{ $index }}][value]" value="{{ $value }}" @checked((string) $stored === (string) $value) class="peer sr-only"><span class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-emerald-300 bg-white text-sm font-semibold text-emerald-700 peer-checked:bg-emerald-600 peer-checked:text-white">{{ $value }}</span></label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('respondentCompetitorForm');
    if (!root) return;
    const names = root.querySelector('[data-competitor-name-list]');
    const questions = [...root.querySelectorAll('[data-dynamic-question]')];
    const ratingHtml = (questionId, index) => [1,2,3,4,5,6,7,0].map(value => `${value === 0 ? '<span class="mx-2 h-10 border-l border-gray-300"></span>' : ''}<label class="cursor-pointer"><input required type="radio" name="answers[${questionId}][${index}][value]" value="${value}" class="peer sr-only"><span class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-emerald-300 bg-white text-sm font-semibold text-emerald-700 peer-checked:bg-emerald-600 peer-checked:text-white">${value}</span></label>`).join('');
    const syncName = (index, value) => root.querySelectorAll(`[data-competitor-display="${index}"]`).forEach(el => el.textContent = value || `Kompetitor ${Number(index)+1}`);
    names.addEventListener('input', e => { const row=e.target.closest('[data-competitor-name-row]'); if(row && e.target.name.endsWith('[name]')) syncName(row.dataset.competitorNameRow, e.target.value); });
    root.addEventListener('click', e => { const remove=e.target.closest('[data-remove-respondent-competitor]'); if(!remove) return; if(names.children.length <= 1) return alert('Minimal satu kompetitor.'); const index=remove.dataset.removeRespondentCompetitor; root.querySelectorAll(`[data-competitor-name-row="${index}"],[data-rating-row="${index}"]`).forEach(el=>el.remove()); });
    root.querySelector('[data-add-respondent-competitor]').addEventListener('click', () => {
        if(names.children.length >= 10) return alert('Maksimal 10 kompetitor.');
        const index=Number(root.dataset.nextIndex++), number=names.children.length+1;
        names.insertAdjacentHTML('beforeend', `<div data-competitor-name-row="${index}" class="flex items-center gap-2 rounded-lg border border-blue-200 bg-white p-3"><span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-100 font-semibold text-blue-700">${number}</span><input type="hidden" name="respondent_competitors[${index}][id]" value=""><input required maxlength="150" name="respondent_competitors[${index}][name]" placeholder="Nama kompetitor" class="min-w-0 flex-1 rounded-lg border border-gray-300 px-3 py-2"><button type="button" data-remove-respondent-competitor="${index}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600"><i class="fa-solid fa-trash"></i></button></div>`);
        questions.forEach(section => section.querySelector('[data-dynamic-rating-list]').insertAdjacentHTML('beforeend', `<div data-rating-row="${index}" class="grid gap-4 px-5 py-4 md:grid-cols-[minmax(12rem,18rem)_1fr] md:items-center"><div class="flex items-center gap-3"><span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600"><i class="fa-solid fa-building"></i></span><span data-competitor-display="${index}" class="font-semibold text-gray-700">Kompetitor ${number}</span></div><div class="flex flex-wrap items-center justify-center gap-2" data-option-group>${ratingHtml(section.dataset.dynamicQuestion,index)}</div></div>`));
    });
});
</script>
@endpush
