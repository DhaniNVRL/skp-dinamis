@php
    $mapKey = $form->id . '-' . $question->id;

    $activeSubunitIds = collect(
        $activeMapSubUnit[$mapKey] ?? []
    )->map(
        fn ($id) => (int) $id
    );

    $allSubunitIds = $allSubunits
        ->pluck('id')
        ->map(
            fn ($id) => (int) $id
        );

    $allAreActive = $allSubunitIds->isNotEmpty()
        && $allSubunitIds->every(
            fn ($id) => $activeSubunitIds->contains($id)
        );

    /*
    |--------------------------------------------------------------------------
    | Question Number
    |--------------------------------------------------------------------------
    |
    | Nomor pertanyaan diambil dari gabungan:
    |
    | no_header + no
    |
    | Contoh:
    | A + 1 = A1
    | A + 2 = A2
    | B + 1 = B1
    |
    | Tidak menggunakan $loop->iteration.
    |
    */
    $questionNumber = trim(
        (string) ($question->no_header ?? '')
        .
        (string) ($question->no ?? '')
    );

    /*
    |--------------------------------------------------------------------------
    | Question Type
    |--------------------------------------------------------------------------
    */
    $questionTypeId = (int) (
        $question->questiontype_id
        ?? $question->id_questiontypes
        ?? 0
    );

    $isHeader = $questionTypeId === 1;

    /*
    |--------------------------------------------------------------------------
    | Form Type
    |--------------------------------------------------------------------------
    */
    $formTypeId = (int) (
        $form->formtype_id
        ?? $form->id_formtype
        ?? 0
    );

    /*
    |--------------------------------------------------------------------------
    | Bubble Number Rule
    |--------------------------------------------------------------------------
    |
    | CASE:
    |
    | FORM TYPE ID = 1
    | Semua question type tetap menampilkan bubble,
    | termasuk questiontype_id = 1.
    |
    | FORM TYPE ID != 1
    | questiontype_id = 1 tidak menampilkan bubble.
    |
    | FORM TYPE ID != 1
    | questiontype_id selain 1 tetap menampilkan bubble.
    |
    */
    $showQuestionBubble =
        $formTypeId === 1
        || ! $isHeader;

    /*
    |--------------------------------------------------------------------------
    | Question Label
    |--------------------------------------------------------------------------
    |
    | Label "Pertanyaan A1", "Pertanyaan A2", dst.
    | mengikuti aturan bubble.
    |
    */
    $showQuestionLabel =
        $showQuestionBubble
        && $questionNumber !== '';

    /*
    |--------------------------------------------------------------------------
    | Header Penilaian Pelanggan hanya mempunyai satu konfigurasi
    |--------------------------------------------------------------------------
    |
    | Form type 2 dan 3 memang dinilai per Sub Unit.
    |
    | Namun pertanyaan bertipe Header / Title adalah judul bersama,
    | sehingga toggle tidak boleh diulang untuk setiap Sub Unit.
    |
    */
    $usesSingleHeaderToggle =
        $isHeader
        && in_array($formTypeId, [2, 3], true);

    $usesPerSubUnitToggle =
        $isPerSubUnit
        && ! $usesSingleHeaderToggle;

    /*
    |--------------------------------------------------------------------------
    | Resolve Unit Name
    |--------------------------------------------------------------------------
    */
    $resolvedUnitName =
        $unitName
        ?? data_get($unit ?? null, 'name')
        ?? data_get($units ?? null, 'name')
        ?? '-';
@endphp


<div class="rounded-lg border border-gray-200">

    {{-- QUESTION HEADER --}}
    <div class="flex items-start gap-3 border-b border-gray-100 bg-gray-50 px-4 py-3">

        {{-- QUESTION NUMBER BUBBLE --}}
        @if ($showQuestionBubble && $questionNumber !== '')
            <div
                class="flex h-8 min-w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 px-2 text-xs font-bold text-blue-700"
            >
                {{ $questionNumber }}
            </div>
        @endif


        <div class="min-w-0 flex-1">

            {{-- QUESTION LABEL --}}
            @if ($showQuestionLabel)
                <div class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">
                    Pertanyaan {{ $questionNumber }}
                </div>
            @endif


            {{-- QUESTION NAME --}}
            <div class="font-medium text-gray-800">
                {{ $question->name }}
            </div>

        </div>

    </div>


    @if ($usesPerSubUnitToggle)

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

                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500"
                        >
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
                            'scopeType' => 'Sub Unit',
                            'targetNames' => [$subunit->name],
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

                <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-purple-100 text-purple-600"
                >
                    <i class="fa-solid fa-layer-group"></i>
                </div>


                <div>

                    <div class="text-sm font-medium text-gray-700">
                        {{ $usesSingleHeaderToggle
                            ? 'Judul berlaku untuk seluruh Sub Unit'
                            : 'Berlaku untuk seluruh Sub Unit' }}
                    </div>


                    <div
                        data-toggle-status
                        class="text-xs {{ $allAreActive ? 'text-green-600' : 'text-red-500' }}"
                    >
                        {{ $allAreActive
                            ? 'Ditampilkan'
                            : 'Disembunyikan' }}
                    </div>

                </div>

            </div>


            @include(
                'admin.subunit.hide-and-show.partials.toggle-button',
                [
                    'formId' => $form->id,
                    'questionId' => $question->id,
                    'subunitIds' => $allSubunitIds->all(),
                    'scopeType' => 'Unit',
                    'targetNames' => [$resolvedUnitName],
                    'isActive' => $allAreActive,
                ]
            )

        </div>

    @endif

</div>
