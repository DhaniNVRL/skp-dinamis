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
                    Penilaian setiap kompetitor dengan skala 1–7.
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

                            @foreach ([1, 2, 3, 4, 5, 6, 7, 0] as $value)
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
                                            'admin.units.question.partials.forms.competitor-1-7.options.competitor-action',
                                            [
                                                'competitor' => $competitor,
                                                'form' => $form,
                                            ]
                                        )

                                    </div>

                                </td>

                                {{-- Preview nilai --}}
                                @foreach ([1, 2, 3, 4, 5, 6, 7, 0] as $value)

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

            <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-6 text-center">

                <i class="fa-solid fa-building-circle-exclamation text-2xl text-gray-400"></i>

                <p class="mt-3 text-sm font-medium text-gray-600">
                    Belum ada kompetitor
                </p>

                <p class="mt-1 text-xs text-gray-500">
                    Tambahkan kompetitor agar tabel penilaian dapat ditampilkan.
                </p>

            </div>

        @endif

    </div>

</div>