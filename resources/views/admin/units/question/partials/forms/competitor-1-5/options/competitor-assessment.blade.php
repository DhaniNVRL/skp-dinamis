<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

    {{-- Header pertanyaan --}}
    <div class="flex items-start justify-between gap-4 border-b border-gray-200 px-5 py-4">

        <div class="flex min-w-0 items-start gap-3">

            <span
                class="inline-flex min-w-12 shrink-0 items-center
                       justify-center rounded-lg bg-violet-100
                       px-2.5 py-1 text-sm font-semibold text-violet-700"
            >
                {{ $question->no_header }}{{ $question->no }}
            </span>

            <div>
                <h4 class="font-semibold text-gray-800">
                    {{ $question->name }}
                </h4>

                <p class="mt-1 text-xs text-gray-500">
                    Penilaian setiap kompetitor dengan skala 1–5.
                </p>
            </div>

        </div>

        @include(
            'admin.units.question.partials.forms.question-action',
            [
                'question' => $question,
                'form' => $form,
            ]
        )

    </div>

    <div class="p-5">

        @if ($competitors->isNotEmpty())

            <div class="overflow-x-auto rounded-lg border border-gray-200">

                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-100">
                        <tr>
                            <th
                                class="min-w-48 px-4 py-3 text-left
                                       text-xs font-semibold uppercase text-gray-600"
                            >
                                Kompetitor
                            </th>

                            @foreach ([1, 2, 3, 4, 5, 0] as $value)
                                <th
                                    class="w-14 px-2 py-3 text-center
                                           text-xs font-semibold text-gray-600"
                                >
                                    {{ $value }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 bg-white">

                        @foreach ($competitors as $competitor)

                            <tr class="transition hover:bg-gray-50">

                                {{-- Nama dan action competitor --}}
                                <td class="px-4 py-3">

                                    <div class="flex items-center justify-between gap-3">

                                        <div class="flex min-w-0 items-center gap-3">

                                            <span
                                                class="inline-flex h-8 w-8 shrink-0 items-center
                                                    justify-center rounded-lg bg-violet-100
                                                    text-violet-600"
                                            >
                                                <i class="fa-solid fa-building text-xs"></i>
                                            </span>

                                            <span class="truncate text-sm font-medium text-gray-700">
                                                {{ $competitor->name }}
                                            </span>

                                        </div>

                                        @include(
                                            'admin.units.question.partials.forms.competitor-1-5.options.competitor-action',
                                            [
                                                'competitor' => $competitor,
                                                'form' => $form,
                                            ]
                                        )

                                    </div>

                                </td>

                                {{-- Preview nilai --}}
                                @foreach ([1, 2, 3, 4, 5, 0] as $value)

                                    <td class="px-2 py-3 text-center">

                                        <input
                                            type="radio"
                                            disabled
                                            name="preview_competitor_{{ $question->id }}_{{ $competitor->id }}"
                                            value="{{ $value }}"
                                            class="h-4 w-4 border-gray-300 text-violet-600"
                                        >

                                    </td>

                                @endforeach

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="min-w-64 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                                Kompetitor
                            </th>
                                <th class="w-14 px-2 py-3 text-center text-xs font-semibold text-gray-600">
                                    1
                                </th>                                <th class="w-14 px-2 py-3 text-center text-xs font-semibold text-gray-600">
                                    2
                                </th>                                <th class="w-14 px-2 py-3 text-center text-xs font-semibold text-gray-600">
                                    3
                                </th>                                <th class="w-14 px-2 py-3 text-center text-xs font-semibold text-gray-600">
                                    4
                                </th>                                <th class="w-14 px-2 py-3 text-center text-xs font-semibold text-gray-600">
                                    5
                                </th>                                <th class="w-14 px-2 py-3 text-center text-xs font-semibold text-gray-600 border-l border-gray-300">
                                    0
                                </th>                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-400">
                                        <i class="fa-solid fa-building-circle-exclamation text-xs"></i>
                                    </span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">Belum ada kompetitor</p>
                                        <p class="mt-0.5 text-xs text-gray-500">Tambahkan nama kompetitor untuk mengaktifkan penilaian skala 1–5.</p>
                                    </div>
                                </div>
                            </td>
                                <td class="px-2 py-4 text-center">
                                    <input type="radio" disabled aria-label="Nilai 1" class="h-4 w-4 border-gray-300 text-violet-600">
                                </td>                                <td class="px-2 py-4 text-center">
                                    <input type="radio" disabled aria-label="Nilai 2" class="h-4 w-4 border-gray-300 text-violet-600">
                                </td>                                <td class="px-2 py-4 text-center">
                                    <input type="radio" disabled aria-label="Nilai 3" class="h-4 w-4 border-gray-300 text-violet-600">
                                </td>                                <td class="px-2 py-4 text-center">
                                    <input type="radio" disabled aria-label="Nilai 4" class="h-4 w-4 border-gray-300 text-violet-600">
                                </td>                                <td class="px-2 py-4 text-center">
                                    <input type="radio" disabled aria-label="Nilai 5" class="h-4 w-4 border-gray-300 text-violet-600">
                                </td>                                <td class="px-2 py-4 text-center border-l border-gray-300">
                                    <input type="radio" disabled aria-label="Nilai 0" class="h-4 w-4 border-gray-300 text-violet-600">
                                </td>                        </tr>
                    </tbody>
                </table>
            </div>

        @endif

    </div>

</div>