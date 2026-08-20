@php
    $perSubUnitFormTypes = [
        2,
        3,
        8,
        9,
        10,
    ];

    $globalFormTypes = [
        1,
        4,
        5,
        6,
        7,
        11,
        12,
        13,
        14,
    ];

    $totalVisibleForms = 0;
@endphp

<div
    id="showQuestionContent"
    class="space-y-6"
>
    {{-- DEBUG DATA --}}
    <div class="rounded-lg border border-yellow-300 bg-yellow-50 p-4 text-sm text-yellow-800">
        <div>
            Jumlah Form:
            <strong>{{ isset($forms) ? $forms->count() : 0 }}</strong>
        </div>

        <div>
            Jumlah Sub Unit:
            <strong>{{ isset($allSubunits) ? $allSubunits->count() : 0 }}</strong>
        </div>

        <div>
            Jumlah Mapping Aktif:
            <strong>{{ isset($activeMapSubUnit) ? count($activeMapSubUnit) : 0 }}</strong>
        </div>
    </div>

    @if (!isset($forms) || $forms->isEmpty())
        @include(
            'admin.subunit.show-question.partials.empty',
            [
                'icon' => 'fa-clipboard-question',
                'title' => 'Form belum tersedia',
                'message' => 'Belum ada Form untuk Unit ini.',
            ]
        )
    @elseif (!isset($allSubunits) || $allSubunits->isEmpty())
        @include(
            'admin.subunit.show-question.partials.empty',
            [
                'icon' => 'fa-building-circle-xmark',
                'title' => 'Sub Unit belum tersedia',
                'message' => 'Tambahkan Sub Unit terlebih dahulu.',
            ]
        )
    @else
        @foreach ($forms->sortBy('no_urut') as $form)
            @php
                $formTypeId = (int) (
                    $form->formtype_id
                    ?? $form->id_formtype
                    ?? 0
                );

                $isPerSubUnit = in_array(
                    $formTypeId,
                    $perSubUnitFormTypes,
                    true
                );

                $isGlobal = in_array(
                    $formTypeId,
                    $globalFormTypes,
                    true
                );

                $activeQuestions = $form->questions
                    ->filter(function ($question) use (
                        $form,
                        $activeMapSubUnit
                    ) {
                        $key = (int) $form->id
                            . '-'
                            . (int) $question->id;

                        return count(
                            $activeMapSubUnit[$key] ?? []
                        ) > 0;
                    })
                    ->values();

                $descriptionContent = $form->description?->content
                    ?? $form->description?->description
                    ?? $form->description?->text
                    ?? null;

                $hasDescription = filled($descriptionContent);

                $canShowForm =
                    $activeQuestions->isNotEmpty()
                    || $hasDescription;

                if ($canShowForm) {
                    $totalVisibleForms++;
                }
            @endphp

            @if ($canShowForm)
                @include(
                    'admin.subunit.show-question.partials.form-card',
                    [
                        'form' => $form,
                        'formTypeId' => $formTypeId,
                        'isPerSubUnit' => $isPerSubUnit,
                        'isGlobal' => $isGlobal,
                        'activeQuestions' => $activeQuestions,
                        'allSubunits' => $allSubunits,
                        'activeMapSubUnit' => $activeMapSubUnit,
                        'competitors' => $competitors,
                    ]
                )
            @endif
        @endforeach

        @if ($totalVisibleForms === 0)
            @include(
                'admin.subunit.show-question.partials.empty',
                [
                    'icon' => 'fa-eye-slash',
                    'title' => 'Belum ada pertanyaan aktif',
                    'message' => 'Aktifkan pertanyaan pada tab Hide and Show.',
                ]
            )
        @endif
    @endif
</div>
