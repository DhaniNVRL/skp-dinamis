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
    ];

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

    /*
    |--------------------------------------------------------------------------
    | Resolve Unit Name
    |--------------------------------------------------------------------------
    |
    | Mencegah:
    | Undefined variable $unitName
    |
    | Mendukung jika parent mengirim:
    | - $unitName
    | - $unit
    | - $units
    */
    $resolvedUnitName =
        $unitName
        ?? data_get($unit ?? null, 'name')
        ?? data_get($units ?? null, 'name')
        ?? '-';

    /*
    |--------------------------------------------------------------------------
    | Urutkan Pertanyaan
    |--------------------------------------------------------------------------
    |
    | no_header:
    | A, B, C, D, ...
    |
    | no menggunakan natural sort:
    | 0
    | 1
    | 2
    | 3
    | 3.1
    | 3.2
    | 3.9
    | 3.10
    | 3.11
    |
    | Nomor kembar tetap diperbolehkan.
    | Jika no_header + no sama, ID menjadi tie breaker.
    */
    $sortedQuestions = collect(
        $form->questions ?? []
    )
        ->sort(function ($first, $second) {

            /*
            |--------------------------------------------------------------------------
            | 1. Sort no_header
            |--------------------------------------------------------------------------
            */
            $firstHeader = strtoupper(
                trim(
                    (string) (
                        $first->no_header ?? ''
                    )
                )
            );

            $secondHeader = strtoupper(
                trim(
                    (string) (
                        $second->no_header ?? ''
                    )
                )
            );

            $headerComparison = strnatcasecmp(
                $firstHeader,
                $secondHeader
            );

            if ($headerComparison !== 0) {
                return $headerComparison;
            }

            /*
            |--------------------------------------------------------------------------
            | 2. Sort no sebagai VARCHAR secara natural
            |--------------------------------------------------------------------------
            */
            $firstNumber = trim(
                (string) (
                    $first->no ?? '0'
                )
            );

            $secondNumber = trim(
                (string) (
                    $second->no ?? '0'
                )
            );

            $numberComparison = strnatcasecmp(
                $firstNumber,
                $secondNumber
            );

            if ($numberComparison !== 0) {
                return $numberComparison;
            }

            /*
            |--------------------------------------------------------------------------
            | 3. Jika nomor kembar, gunakan ID
            |--------------------------------------------------------------------------
            */
            return (int) $first->id
                <=>
                (int) $second->id;
        })
        ->values();
@endphp


<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

    {{-- FORM HEADER --}}
    <div class="border-b border-gray-200 bg-gray-50 px-5 py-4">

        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">

            <div>

                <h3 class="font-semibold text-gray-900">
                    {{ $form->name }}
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $form->formtype->name ?? 'Tanpa tipe form' }}
                </p>

            </div>


            <span
                class="inline-flex w-fit items-center rounded-full px-3 py-1 text-xs font-semibold
                    {{ $isPerSubUnit
                        ? 'bg-blue-100 text-blue-700'
                        : 'bg-purple-100 text-purple-700' }}"
            >

                @if ($isPerSubUnit)

                    <i class="fa-solid fa-building mr-2"></i>
                    Per Sub Unit

                @else

                    <i class="fa-solid fa-layer-group mr-2"></i>
                    Pertanyaan Global

                @endif

            </span>

        </div>

    </div>


    {{-- QUESTIONS --}}
    <div class="space-y-4 p-5">

        @forelse ($sortedQuestions as $question)

            @include(
                'admin.subunit.hide-and-show.partials.question-card',
                [
                    'form' => $form,

                    'question' => $question,

                    'allSubunits' =>
                        $allSubunits,

                    'activeMapSubUnit' =>
                        $activeMapSubUnit,

                    'isPerSubUnit' =>
                        $isPerSubUnit,

                    'questionIteration' =>
                        $loop->iteration,

                    /*
                    |--------------------------------------------------------------------------
                    | Kirim nama unit yang sudah resolved
                    |--------------------------------------------------------------------------
                    */
                    'unitName' =>
                        $resolvedUnitName,
                ]
            )

        @empty

            <div
                class="rounded-lg border border-dashed
                       border-gray-300 px-5 py-8
                       text-center text-sm text-gray-500"
            >
                Form ini belum memiliki pertanyaan.
            </div>

        @endforelse

    </div>

</div>

