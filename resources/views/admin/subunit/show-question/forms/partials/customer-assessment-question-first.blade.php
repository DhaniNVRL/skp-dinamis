@php
    $scaleMaximum = (int) ($scaleMaximum ?? 5);
@endphp

<div data-customer-assessment class="space-y-5">
    @forelse ($questions->groupBy('no_header') as $group)
        @php
            $orderedGroup = $group->sortBy('no')->values();
            $headerQuestion = $orderedGroup->first(function ($question) {
                return (int) ($question->questiontype_id ?? $question->id_questiontypes ?? 0) === 1;
            });
            $visibleQuestions = $orderedGroup
                ->reject(function ($question) {
                    return (int) ($question->questiontype_id ?? $question->id_questiontypes ?? 0) === 1;
                })
                ->filter(function ($question) use ($form, $activeMapSubUnit) {
                    $key = $form->id . '-' . $question->id;

                    return collect($activeMapSubUnit[$key] ?? [])->isNotEmpty();
                })
                ->values();
        @endphp

        @if ($visibleQuestions->isNotEmpty())
            @if ($headerQuestion)
                <div class="rounded-xl border border-blue-200 bg-blue-50 px-5 py-4">
                    <h2 class="text-center text-lg font-bold text-gray-900">
                        {{ $headerQuestion->name }}
                    </h2>
                </div>
            @endif

        @foreach ($visibleQuestions as $question)
            @php
                $questionTypeId = (int) ($question->questiontype_id ?? $question->id_questiontypes ?? 0);
                $questionNumber = trim((string) ($question->no_header ?? '') . (string) ($question->no ?? ''));
                $key = $form->id . '-' . $question->id;
                $activeSubunitIds = collect($activeMapSubUnit[$key] ?? [])
                    ->map(fn ($id) => (int) $id)
                    ->unique();
                $questionSubunits = $subunits
                    ->filter(fn ($item) => $activeSubunitIds->contains((int) $item->id))
                    ->values();
            @endphp

            @continue($questionSubunits->isEmpty())

            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <header class="border-b border-gray-200 px-5 py-4">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-9 min-w-9 shrink-0 items-center justify-center rounded-lg bg-blue-100 px-2 text-sm font-semibold text-blue-700">
                            {{ $questionNumber }}
                        </span>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-gray-900">{{ $question->name }}</h3>
                            <p class="mt-1 text-xs text-gray-500">
                                @if ($questionTypeId === 2)
                                    Penilaian Kepentingan dan Kinerja
                                @elseif ($questionTypeId === 3)
                                    Penilaian Kepentingan dan Kinerja dengan alasan
                                @elseif ($questionTypeId === 4)
                                    Penilaian Kepentingan dan Kinerja dengan pilihan alasan
                                @elseif ($questionTypeId === 5)
                                    Penilaian satu indikator
                                @elseif ($questionTypeId === 6)
                                    Jawaban penilaian
                                @endif
                            </p>
                        </div>
                    </div>
                </header>

                <div class="space-y-5 bg-gray-50 p-5">
                    @foreach ($questionSubunits as $subunit)
                        @php
                            $scopeId = (int) $subunit->id;
                            $questionScope = $question->id . '_' . $scopeId;
                        @endphp

                        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                            <div class="flex items-center gap-3 border-b border-blue-200 bg-blue-50 px-5 py-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                                    <i class="fa-solid fa-building"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold uppercase tracking-wide text-blue-500">Sub Unit</div>
                                    <h4 class="truncate font-semibold text-gray-900">{{ $subunit->name }}</h4>
                                </div>
                            </div>

                            <div class="p-4 md:p-5">
                                @if (in_array($questionTypeId, [2, 3, 4], true))
                                    @include(
                                        'admin.subunit.show-question.forms.partials.importance-performance',
                                        [
                                            'question' => $question,
                                            'scopeId' => $scopeId,
                                            'scaleMaximum' => $scaleMaximum,
                                        ]
                                    )

                                    @if ($questionTypeId === 3)
                                        <div class="mt-5 rounded-xl border border-gray-200 bg-gray-50 p-4">
                                            <label class="mb-2 block text-sm font-semibold text-gray-700">Alasan</label>
                                            <textarea rows="3" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm" placeholder="Tuliskan alasan penilaian Anda..."></textarea>
                                        </div>
                                    @elseif ($questionTypeId === 4)
                                        <div class="mt-5 rounded-xl border border-gray-200 bg-gray-50 p-4">
                                            <h5 class="mb-3 text-sm font-semibold text-gray-800">Pilihan Alasan</h5>
                                            <div class="space-y-2">
                                                @forelse ($question->options as $option)
                                                    <label class="flex items-start gap-3 rounded-lg border border-gray-200 bg-white p-3 text-sm text-gray-700">
                                                        <input type="checkbox" class="mt-0.5 rounded border-gray-300 text-indigo-600">
                                                        <span>{{ $option->answer_text }}</span>
                                                    </label>
                                                @empty
                                                    <p class="text-sm text-gray-500">Pilihan alasan belum tersedia.</p>
                                                @endforelse
                                            </div>
                                        </div>
                                    @endif
                                @elseif ($questionTypeId === 5)
                                    <div class="flex justify-center rounded-xl border border-indigo-200 bg-indigo-50 p-5">
                                        @include(
                                            'admin.subunit.show-question.forms.partials.scale',
                                            [
                                                'question' => $question,
                                                'maximum' => $scaleMaximum,
                                                'includeZero' => true,
                                                'name' => "indicator_{$questionScope}",
                                            ]
                                        )
                                    </div>
                                @elseif ($questionTypeId === 6)
                                    <textarea rows="4" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm" placeholder="Tulis jawaban Anda..."></textarea>
                                @else
                                    <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-700">
                                        Question Type {{ $questionTypeId }} belum didukung: {{ $question->name }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
        @endif
    @empty
        @include('admin.subunit.show-question.forms.partials.empty')
    @endforelse
</div>
