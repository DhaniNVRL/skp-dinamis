<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    {{-- FORM HEADER --}}
    <div class="border-b border-gray-200 bg-gray-50 px-5 py-5 md:px-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="min-w-0">
                <div class="flex items-center gap-3">
                    @if (!empty($form->no_urut))
                        <span class="flex h-8 min-w-8 items-center justify-center rounded-full bg-indigo-100 px-2 text-sm font-bold text-indigo-600">
                            {{ $form->no_urut }}
                        </span>
                    @endif

                    <h2 class="text-xl font-semibold text-gray-800 md:text-2xl">
                        {{ $form->name }}
                    </h2>
                </div>

                @if ($form->formtype)
                    <div class="mt-3">
                        <span class="inline-flex flex-wrap items-center gap-2 rounded-full bg-indigo-100 px-3 py-1 text-sm text-indigo-700">
                            <span class="font-medium">
                                {{ $form->formtype->name }}
                            </span>

                            @if (!empty($form->formtype->description))
                                <span class="text-indigo-300">
                                    •
                                </span>

                                <span>
                                    {{ $form->formtype->description }}
                                </span>
                            @endif
                        </span>
                    </div>
                @endif
            </div>

            <span
                class="inline-flex w-fit shrink-0 items-center rounded-full px-3 py-1 text-xs font-semibold
                    {{ $isPerSubUnit
                        ? 'bg-blue-100 text-blue-700'
                        : 'bg-purple-100 text-purple-700' }}"
            >
                @if ($isPerSubUnit)
                    <i class="fa-solid fa-building mr-2"></i>
                    Per Sub Unit
                @else
                    <i class="fa-solid fa-layer-group mr-2"></i>
                    Global
                @endif
            </span>
        </div>
    </div>

    <div class="space-y-5 p-5 md:p-6">
        {{-- DESCRIPTION --}}
        @include(
            'admin.subunit.show-question.partials.form-description',
            ['form' => $form]
        )

        @if ($isPerSubUnit)
            {{--
            |--------------------------------------------------------------------------
            | FORM PER SUB UNIT
            |--------------------------------------------------------------------------
            --}}
            @php
                $visibleSubUnitCount = 0;
                $allSubunitIds = $allSubunits
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values();

                $formTypeId = (int) (
                    $form->formtype_id
                    ?? $form->id_formtype
                    ?? 0
                );
                $usesSingleHeader = in_array($formTypeId, [2, 3], true);

                /*
                 * Form Penilaian Pelanggan 1-5 dan 1-7 memakai satu Header
                 * bersama. Header hanya aktif jika mapping-nya lengkap untuk
                 * seluruh Sub Unit, lalu ditampilkan satu kali di atas daftar.
                 */
                $globalHeaderQuestions = $activeQuestions
                    ->filter(function ($question) use (
                        $form,
                        $activeMapSubUnit,
                        $allSubunitIds,
                        $usesSingleHeader
                    ) {
                        if (! $usesSingleHeader) {
                            return false;
                        }

                        $questionTypeId = (int) (
                            $question->questiontype_id
                            ?? $question->id_questiontypes
                            ?? 0
                        );

                        if ($questionTypeId !== 1) {
                            return false;
                        }

                        $key = $form->id . '-' . $question->id;
                        $activeSubunitIds = collect($activeMapSubUnit[$key] ?? [])
                            ->map(fn ($id) => (int) $id)
                            ->unique();

                        return $allSubunitIds->isNotEmpty()
                            && $allSubunitIds->every(
                                fn ($id) => $activeSubunitIds->contains($id)
                            );
                    })
                    ->values();

                $perSubUnitQuestions = $activeQuestions
                    ->reject(function ($question) use ($usesSingleHeader) {
                        if (! $usesSingleHeader) {
                            return false;
                        }

                        return (int) (
                            $question->questiontype_id
                            ?? $question->id_questiontypes
                            ?? 0
                        ) === 1;
                    })
                    ->values();
            @endphp

            @if ($usesSingleHeader)
                @php
                    $visibleQuestionCount = $perSubUnitQuestions
                        ->filter(function ($question) use ($form, $activeMapSubUnit) {
                            $key = $form->id . '-' . $question->id;

                            return collect($activeMapSubUnit[$key] ?? [])->isNotEmpty();
                        })
                        ->count();
                    $visibleSubUnitCount = $visibleQuestionCount;
                    $customerAssessmentQuestions = $globalHeaderQuestions
                        ->concat($perSubUnitQuestions)
                        ->values();
                @endphp

                @if ($visibleQuestionCount > 0)
                    @include(
                        'admin.subunit.show-question.forms.partials.customer-assessment-question-first',
                        [
                            'form' => $form,
                            'questions' => $customerAssessmentQuestions,
                            'subunits' => $allSubunits,
                            'activeMapSubUnit' => $activeMapSubUnit,
                            'scaleMaximum' => $formTypeId === 3 ? 7 : 5,
                        ]
                    )
                @endif
            @else
                @foreach ($allSubunits as $subunit)
                    @php
                        $subunitQuestions = $perSubUnitQuestions
                            ->filter(function ($question) use (
                                $form,
                                $subunit,
                                $activeMapSubUnit
                            ) {
                                $key = $form->id . '-' . $question->id;
                                $activeSubunitIds = collect(
                                    $activeMapSubUnit[$key] ?? []
                                )->map(fn ($id) => (int) $id);

                                return $activeSubunitIds->contains((int) $subunit->id);
                            })
                            ->values();

                        if ($subunitQuestions->isNotEmpty()) {
                            $visibleSubUnitCount++;
                        }
                    @endphp

                    @if ($subunitQuestions->isNotEmpty())
                        <section class="overflow-hidden rounded-xl border border-blue-200">
                            <div class="flex items-center gap-3 border-b border-blue-200 bg-blue-50 px-5 py-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                                    <i class="fa-solid fa-building"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold uppercase tracking-wide text-blue-500">
                                        Sub Unit
                                    </div>
                                    <h3 class="truncate font-semibold text-gray-800">
                                        {{ $subunit->name }}
                                    </h3>
                                </div>
                            </div>

                            <div class="bg-gray-50 p-4 md:p-5">
                                @include(
                                    'admin.subunit.show-question.partials.form-content',
                                    [
                                        'form' => $form,
                                        'formTypeId' => $formTypeId,
                                        'questions' => $subunitQuestions,
                                        'subunit' => $subunit,
                                        'subunits' => collect([$subunit]),
                                        'subunitIds' => collect([$subunit->id]),
                                        'activeMapSubUnit' => $activeMapSubUnit,
                                        'competitors' => $competitors,
                                    ]
                                )
                            </div>
                        </section>
                    @endif
                @endforeach
            @endif
            @if ($visibleSubUnitCount === 0 && blank($form->description?->content ?? $form->description?->description ?? $form->description?->text))
                <div class="rounded-lg border border-dashed border-gray-300 px-5 py-10 text-center text-sm text-gray-500">
                    Tidak ada pertanyaan aktif untuk Sub Unit pada form ini.
                </div>
            @endif
        @else
            {{--
            |--------------------------------------------------------------------------
            | FORM GLOBAL
            |--------------------------------------------------------------------------
            --}}
            @if ($activeQuestions->isNotEmpty())
                <section class="rounded-xl border border-gray-200 bg-gray-50 p-4 md:p-5">
                    @include(
                        'admin.subunit.show-question.partials.form-content',
                        [
                            'form' => $form,
                            'formTypeId' => $formTypeId,
                            'questions' => $activeQuestions,
                            'subunits' => $allSubunits,
                            'subunitIds' => $allSubunits->pluck('id'),
                            'activeMapSubUnit' => $activeMapSubUnit,
                            'competitors' => $competitors,
                        ]
                    )
                </section>
            @endif
        @endif
    </div>
</div>
