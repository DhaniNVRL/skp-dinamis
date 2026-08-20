<div class="space-y-5" data-respondent-competitor-preview>
    <div class="rounded-xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm text-blue-700">
        <div class="flex items-start gap-3">
            <i class="fa-solid fa-circle-info mt-0.5"></i>
            <div>
                <p class="font-semibold">Kompetitor ditentukan oleh responden</p>
                <p class="mt-1 text-blue-600">
                    Nama dan jumlah kompetitor dapat berbeda untuk setiap responden. Baris di bawah hanya contoh tampilan pengisian.
                </p>
            </div>
        </div>
    </div>

    @forelse ($questions->groupBy('no_header') as $group)
        @foreach ($group->filter(fn ($question) => (int) ($question->questiontype_id ?? $question->id_questiontypes ?? 0) === 1)->sortBy('no') as $question)
            <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-5 py-4">
                <h3 class="font-semibold text-gray-800">{{ $question->name }}</h3>
            </div>
        @endforeach

        @foreach ($group->filter(fn ($question) => (int) ($question->questiontype_id ?? $question->id_questiontypes ?? 0) === 2)->sortBy('no') as $question)
            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <header class="border-b border-gray-200 bg-gray-50 px-5 py-4">
                    <div class="flex items-start gap-3">
                        @include('admin.subunit.show-question.forms.partials.question-number', compact('question'))
                        <div>
                            <h3 class="font-semibold leading-6 text-gray-800">{{ $question->name }}</h3>
                            <p class="mt-1 text-xs text-gray-500">Berikan nilai untuk setiap kompetitor yang dimasukkan responden.</p>
                        </div>
                    </div>
                </header>

                <div class="grid gap-4 px-5 py-4 md:grid-cols-[minmax(12rem,18rem)_1fr] md:items-center">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-violet-100 text-violet-600">
                            <i class="fa-solid fa-building text-xs"></i>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Contoh Nama Kompetitor</p>
                            <p class="mt-0.5 text-xs text-gray-500">Diisi oleh responden</p>
                        </div>
                    </div>

                    <div class="min-w-0">
                        @include('admin.subunit.show-question.forms.partials.scale', [
                            'question' => $question,
                            'maximum' => 7,
                            'includeZero' => true,
                            'name' => "respondent_competitor_preview_{$question->id}",
                            'zeroLabel' => null,
                        ])
                    </div>
                </div>
            </section>
        @endforeach
    @empty
        @include('admin.subunit.show-question.forms.partials.empty')
    @endforelse
</div>
