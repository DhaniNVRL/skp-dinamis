@php
    $mapKey = $form->id . '-' . $question->id;

    $activeSubunitIds = collect(
        $activeMapSubUnit[$mapKey] ?? []
    )->map(fn ($id) => (int) $id);

    $allSubunitIds = $allSubunits
        ->pluck('id')
        ->map(fn ($id) => (int) $id);

    $allAreActive = $allSubunitIds->isNotEmpty()
        && $allSubunitIds->every(
            fn ($id) => $activeSubunitIds->contains($id)
        );

    $questionNumber = trim(
        ($question->no_header ?? '') .
        ($question->no ?? '')
    );

    $isHeader = (int) (
        $question->questiontype_id
        ?? $question->id_questiontypes
        ?? 0
    ) === 1;
@endphp

<div class="rounded-lg border border-gray-200">
    {{-- QUESTION HEADER --}}
    <div class="flex items-start gap-3 border-b border-gray-100 bg-gray-50 px-4 py-3">
        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700">
            {{ $loop->iteration }}
        </div>

        <div class="min-w-0 flex-1">
            @if ($questionNumber !== '')
                <div class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">
                    Pertanyaan {{ $questionNumber }}
                </div>
            @endif

            <div class="font-medium text-gray-800">
                {{ $question->name }}
            </div>
        </div>
    </div>

    @if ($isPerSubUnit)
        {{-- SATU BARIS UNTUK SETIAP SUB UNIT --}}
        <div class="divide-y divide-gray-100">
            @foreach ($allSubunits as $subunit)
                @php
                    $isActive = $activeSubunitIds->contains(
                        (int) $subunit->id
                    );
                @endphp

                <div class="flex items-center justify-between gap-4 px-4 py-3">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                            <i class="fa-solid fa-building"></i>
                        </div>

                        <div class="min-w-0">
                            <div class="truncate text-sm font-medium text-gray-700">
                                {{ $subunit->name }}
                            </div>

                            <div
                                data-toggle-status
                                class="text-xs {{ $isActive ? 'text-green-600' : 'text-red-500' }}"
                            >
                                {{ $isActive ? 'Ditampilkan' : 'Disembunyikan' }}
                            </div>
                        </div>
                    </div>

                    @include(
                        'admin.subunit.hide-and-show.partials.toggle-button',
                        [
                            'formId' => $form->id,
                            'questionId' => $question->id,
                            'subunitIds' => [$subunit->id],
                            'isActive' => $isActive,
                        ]
                    )
                </div>
            @endforeach
        </div>
    @else
        {{-- SATU TOGGLE UNTUK SELURUH SUB UNIT --}}
        <div class="flex items-center justify-between gap-4 px-4 py-4">
            <div class="flex min-w-0 items-center gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-purple-100 text-purple-600">
                    <i class="fa-solid fa-layer-group"></i>
                </div>

                <div>
                    <div class="text-sm font-medium text-gray-700">
                        Berlaku untuk seluruh Sub Unit
                    </div>

                    <div
                        data-toggle-status
                        class="text-xs {{ $allAreActive ? 'text-green-600' : 'text-red-500' }}"
                    >
                        {{ $allAreActive ? 'Ditampilkan' : 'Disembunyikan' }}
                    </div>
                </div>
            </div>

            @include(
                'admin.subunit.hide-and-show.partials.toggle-button',
                [
                    'formId' => $form->id,
                    'questionId' => $question->id,
                    'subunitIds' => $allSubunitIds->all(),
                    'isActive' => $allAreActive,
                ]
            )
        </div>
    @endif
</div>