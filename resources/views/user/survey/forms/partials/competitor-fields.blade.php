@php
    /*
    |--------------------------------------------------------------------------
    | NORMALISASI DATA
    |--------------------------------------------------------------------------
    */

    $questions = collect($questions ?? []);
    $competitors = collect($competitors ?? []);
    $answerMap = $answerMap ?? [];

    /*
    |--------------------------------------------------------------------------
    | SKALA PENILAIAN
    |--------------------------------------------------------------------------
    |
    | Nilai dikirim dari:
    |
    | competitor-assessment-1-5.blade.php
    | competitor-assessment-1-7.blade.php
    |
    */

    $scaleValues = collect(
        $scaleValues ?? [1, 2, 3, 4, 5]
    )
        ->map(fn ($value) => (int) $value)
        ->unique()
        ->values();

    /*
    |--------------------------------------------------------------------------
    | PERTANYAAN FORM AKTIF
    |--------------------------------------------------------------------------
    |
    | Mencegah pertanyaan dari form lain ikut ditampilkan.
    |
    */

    $formQuestions = $questions
        ->filter(function ($question) use ($form) {
            return (int) ($question->form_id ?? 0)
                === (int) ($form->id ?? 0);
        })
        ->values();

    /*
    |--------------------------------------------------------------------------
    | KELOMPOKKAN PERTANYAAN
    |--------------------------------------------------------------------------
    */

    $groupedQuestions = $formQuestions
        ->groupBy(function ($question) {
            return trim(
                (string) ($question->no_header ?? '')
            );
        })
        ->sortKeysUsing(function (
            $firstHeader,
            $secondHeader
        ) {
            return strnatcasecmp(
                trim((string) $firstHeader),
                trim((string) $secondHeader)
            );
        });
@endphp

<div
    data-competitor-assessment
    class="space-y-6"
>
    @forelse (
        $groupedQuestions
        as $header => $questionGroup
    )
        @php
            /*
            |--------------------------------------------------------------------------
            | URUTKAN PERTANYAAN
            |--------------------------------------------------------------------------
            |
            | Mendukung urutan:
            |
            | C1, C2, C9, C10, C11, CK
            |
            */

            $sortedQuestionGroup = collect($questionGroup)
                ->sort(function (
                    $firstQuestion,
                    $secondQuestion
                ) {
                    $firstNo = trim(
                        (string) ($firstQuestion->no ?? '')
                    );

                    $secondNo = trim(
                        (string) ($secondQuestion->no ?? '')
                    );

                    $comparison = strnatcasecmp(
                        $firstNo,
                        $secondNo
                    );

                    if ($comparison !== 0) {
                        return $comparison;
                    }

                    return (int) $firstQuestion->id
                        <=> (int) $secondQuestion->id;
                })
                ->values();

            /*
             * Question type 1 digunakan sebagai judul.
             */
            $titleQuestions = $sortedQuestionGroup
                ->filter(function ($question) {
                    return (int) (
                        $question->questiontype_id
                        ?? $question->id_questiontypes
                        ?? 0
                    ) === 1;
                })
                ->values();

            /*
             * Selain type 1 akan menjadi pertanyaan penilaian.
             */
            $assessmentQuestions = $sortedQuestionGroup
                ->reject(function ($question) {
                    return (int) (
                        $question->questiontype_id
                        ?? $question->id_questiontypes
                        ?? 0
                    ) === 1;
                })
                ->values();
        @endphp

        {{-- ========================================================== --}}
        {{-- JUDUL PERTANYAAN --}}
        {{-- ========================================================== --}}

        @foreach ($titleQuestions as $question)
            <div
                class="overflow-hidden rounded-xl
                       border border-blue-200
                       bg-blue-50 shadow-sm"
            >
                <div class="px-5 py-4">
                    <h2
                        class="text-center text-lg
                               font-bold text-gray-800"
                    >
                        {{ $question->name }}
                    </h2>
                </div>
            </div>
        @endforeach

        {{-- ========================================================== --}}
        {{-- TABEL PENILAIAN KOMPETITOR --}}
        {{-- ========================================================== --}}

        @if ($assessmentQuestions->isNotEmpty())
            @if ($competitors->isNotEmpty())
                <section
                    class="overflow-hidden rounded-xl
                           border border-gray-200
                           bg-white shadow-sm"
                >
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full
                                   divide-y divide-gray-200"
                        >
                            <thead class="bg-gray-100">
                                <tr>
                                    <th
                                        scope="col"
                                        class="min-w-80 px-5 py-4
                                               text-left text-xs
                                               font-semibold uppercase
                                               tracking-wide
                                               text-gray-600"
                                    >
                                        Pertanyaan
                                    </th>

                                    @foreach ($competitors as $competitor)
                                        <th
                                            scope="col"
                                            class="min-w-64 px-5 py-4
                                                   text-center text-xs
                                                   font-semibold uppercase
                                                   tracking-wide
                                                   text-gray-600"
                                        >
                                            <div
                                                class="flex flex-col
                                                       items-center gap-2"
                                            >
                                                <span
                                                    class="inline-flex h-9 w-9
                                                           items-center
                                                           justify-center
                                                           rounded-lg
                                                           bg-emerald-100
                                                           text-emerald-600"
                                                >
                                                    <i class="fa-solid fa-building"></i>
                                                </span>

                                                <span>
                                                    {{ $competitor->name }}
                                                </span>
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody
                                class="divide-y divide-gray-200
                                       bg-white"
                            >
                                @foreach ($assessmentQuestions as $question)
                                    @php
                                        $questionNumber = trim(
                                            (string) (
                                                $question->no_header ?? ''
                                            )
                                            . (string) (
                                                $question->no ?? ''
                                            )
                                        );
                                    @endphp

                                    <tr
                                        data-question-container
                                        data-question-type="competitor"
                                        data-question-id="{{ $question->id }}"
                                        class="align-top"
                                    >
                                        {{-- ================================== --}}
                                        {{-- PERTANYAAN --}}
                                        {{-- ================================== --}}

                                        <td class="px-5 py-5">
                                            <div
                                                class="flex items-start
                                                       gap-3"
                                            >
                                                @if ($questionNumber !== '')
                                                    <span
                                                        class="inline-flex
                                                               min-w-10 shrink-0
                                                               items-center
                                                               justify-center
                                                               rounded-lg
                                                               bg-blue-100
                                                               px-2.5 py-1
                                                               text-sm
                                                               font-semibold
                                                               text-blue-700"
                                                    >
                                                        {{ $questionNumber }}
                                                    </span>
                                                @endif

                                                <div class="min-w-0">
                                                    <div
                                                        class="font-semibold
                                                               leading-6
                                                               text-gray-800"
                                                    >
                                                        {{ $question->name }}
                                                    </div>

                                                    <p
                                                        class="mt-1 text-xs
                                                               text-gray-500"
                                                    >
                                                        Berikan nilai untuk
                                                        setiap kompetitor.
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- ================================== --}}
                                        {{-- NILAI PER KOMPETITOR --}}
                                        {{-- ================================== --}}

                                        @foreach ($competitors as $competitor)
                                            @php
                                                /*
                                                 * Ambil jawaban lama.
                                                 *
                                                 * Mendukung beberapa bentuk
                                                 * answerMap yang mungkin sudah
                                                 * digunakan controller.
                                                 */

                                                $storedValue = old(
                                                    "answers.{$question->id}.{$competitor->id}.value",
                                                    data_get(
                                                        $answerMap,
                                                        "{$question->id}.{$competitor->id}.value",
                                                        data_get(
                                                            $answerMap,
                                                            "{$question->id}.0.{$competitor->id}.value",
                                                            data_get(
                                                                $answerMap,
                                                                "{$question->id}.{$competitor->id}.0.value"
                                                            )
                                                        )
                                                    )
                                                );

                                                $inputName =
                                                    'answers['
                                                    . $question->id
                                                    . ']['
                                                    . $competitor->id
                                                    . '][value]';
                                            @endphp

                                            <td
                                                class="px-5 py-5
                                                       align-middle"
                                            >
                                                <div
                                                    data-option-group
                                                    class="flex flex-wrap
                                                           items-center
                                                           justify-center
                                                           gap-2"
                                                >
                                                    @foreach ($scaleValues as $value)
                                                        @php
                                                            $inputId =
                                                                'competitor-'
                                                                . $question->id
                                                                . '-'
                                                                . $competitor->id
                                                                . '-'
                                                                . $value;
                                                        @endphp

                                                        <label
                                                            for="{{ $inputId }}"
                                                            class="cursor-pointer"
                                                        >
                                                            <input
                                                                id="{{ $inputId }}"
                                                                type="radio"
                                                                name="{{ $inputName }}"
                                                                value="{{ $value }}"
                                                                @checked(
                                                                    (string) $storedValue
                                                                    ===
                                                                    (string) $value
                                                                )
                                                                required
                                                                class="peer sr-only"
                                                            >

                                                            <span
                                                                class="inline-flex
                                                                       h-10 w-10
                                                                       items-center
                                                                       justify-center
                                                                       rounded-full
                                                                       border
                                                                       border-emerald-300
                                                                       bg-white
                                                                       text-sm
                                                                       font-semibold
                                                                       text-emerald-700
                                                                       transition
                                                                       hover:bg-emerald-50
                                                                       peer-checked:border-emerald-600
                                                                       peer-checked:bg-emerald-600
                                                                       peer-checked:text-white
                                                                       peer-focus:ring-2
                                                                       peer-focus:ring-emerald-200"
                                                            >
                                                                {{ $value }}
                                                            </span>
                                                        </label>
                                                    @endforeach
                                                </div>

                                                <p
                                                    data-field-error
                                                    class="mt-3 hidden
                                                           text-center
                                                           text-sm
                                                           font-medium
                                                           text-red-600"
                                                >
                                                    Penilaian wajib dipilih.
                                                </p>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @else
                @include(
                    'user.survey.partials.empty',
                    [
                        'message' =>
                            'Data kompetitor belum tersedia.',
                    ]
                )
            @endif
        @endif

    @empty
        @include(
            'user.survey.partials.empty',
            [
                'message' =>
                    'Form ini belum memiliki pertanyaan kompetitor.',
            ]
        )
    @endforelse
</div>
