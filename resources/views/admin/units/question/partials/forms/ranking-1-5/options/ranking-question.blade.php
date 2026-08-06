@php
    $questionOptions = ($question->options ?? collect())
        ->sortBy('no');
@endphp

<div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

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

            <div class="min-w-0">

                <h4 class="font-semibold leading-6 text-gray-800">
                    {{ $question->name }}
                </h4>

                <p class="mt-1 text-xs text-gray-500">
                    Pilih dan urutkan 5 pilihan terbaik.
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

        {{-- Informasi ranking --}}
        <div class="mb-5 rounded-lg border border-violet-200 bg-violet-50 px-4 py-3">

            <div class="flex items-start gap-3">

                <i class="fa-solid fa-ranking-star mt-0.5 text-violet-600"></i>

                <div>
                    <p class="text-sm font-semibold text-violet-800">
                        Ranking 1 sampai 5
                    </p>

                    <p class="mt-1 text-xs leading-5 text-violet-700">
                        Ranking 1 merupakan prioritas tertinggi dan ranking 5
                        merupakan prioritas kelima.
                    </p>
                </div>

            </div>

        </div>

        {{-- Header option --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">

            <div>
                <h5 class="text-sm font-semibold text-gray-800">
                    Daftar Pilihan
                </h5>

                <p class="mt-1 text-xs text-gray-500">
                    Option yang dapat dipilih oleh responden.
                </p>
            </div>

            <button
                type="button"
                data-modal-open="createOptionModal"
                data-question-id="{{ $question->id }}"
                data-question-name="{{ $question->name }}"
                data-form-id="{{ $question->form_id }}"
                data-group-id="{{ $question->group_id }}"
                data-action="{{ route('options.store') }}"
                class="inline-flex items-center gap-2 rounded-lg
                       bg-violet-600 px-3 py-2 text-xs font-medium
                       text-white transition hover:bg-violet-700"
            >
                <i class="fa-solid fa-plus"></i>
                Tambah Option
            </button>

        </div>

        {{-- Daftar option --}}
        <div class="space-y-3">

            @forelse ($questionOptions as $option)

                @php
                    $optionLabel =
                        $option->answer_text
                        ?? 'Pilihan tidak ditemukan';

                    $childLabel =
                        $option->answer_text2
                        ?? 'Tuliskan keterangan tambahan...';

                    $hasChild =
                        (int) ($option->has_child ?? 0) === 1;
                @endphp

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">

                    <div class="flex flex-col gap-4 md:flex-row md:items-start">

                        {{-- Nomor option --}}
                        <div
                            class="inline-flex h-9 min-w-9 shrink-0 items-center
                                   justify-center rounded-lg bg-white
                                   px-2 text-sm font-semibold text-gray-600
                                   shadow-sm ring-1 ring-gray-200"
                        >
                            {{ $option->no }}
                        </div>

                        {{-- Nama option --}}
                        <div class="min-w-0 flex-1">

                            <p class="text-sm font-medium text-gray-800">
                                {{ $optionLabel }}
                            </p>

                            @if ($hasChild)
                                <span
                                    class="mt-1 inline-flex items-center gap-1
                                           text-xs font-medium text-indigo-600"
                                >
                                    <i class="fa-solid fa-align-left"></i>
                                    Memiliki isian lanjutan
                                </span>
                            @endif

                        </div>

                        {{-- Preview pilihan ranking --}}
                        <div class="flex shrink-0 items-center gap-2">

                            <label class="text-xs font-medium text-gray-500">
                                Ranking
                            </label>

                            <select
                                disabled
                                class="w-24 rounded-lg border border-gray-300
                                       bg-white px-3 py-2 text-sm text-gray-600"
                            >
                                <option value="">
                                    Pilih
                                </option>

                                @foreach ([1, 2, 3, 4, 5] as $rank)
                                    <option value="{{ $rank }}">
                                        {{ $rank }}
                                    </option>
                                @endforeach
                            </select>

                        </div>

                        {{-- Action option --}}
                        <div class="flex shrink-0 items-center gap-2">

                            <button
                                type="button"
                                data-modal-open="editOptionModal"
                                data-id="{{ $option->id }}"
                                data-question-id="{{ $question->id }}"
                                data-no="{{ $option->no }}"
                                data-answer-text="{{ $option->answer_text }}"
                                data-answer-text2="{{ $option->answer_text2 }}"
                                data-has-child="{{ $hasChild ? 1 : 0 }}"
                                data-action="{{ route('options.update', [
                                    'id' => $option->id,
                                ]) }}"
                                class="inline-flex h-8 w-8 items-center
                                       justify-center rounded-lg bg-amber-50
                                       text-amber-600 transition hover:bg-amber-100"
                                title="Edit option"
                            >
                                <i class="fa-solid fa-pen text-xs"></i>
                            </button>

                            <button
                                type="button"
                                data-modal-open="deleteOptionModal"
                                data-id="{{ $option->id }}"
                                data-name="{{ $optionLabel }}"
                                data-action="{{ route('options.destroy', [
                                    'id' => $option->id,
                                ]) }}"
                                class="inline-flex h-8 w-8 items-center
                                       justify-center rounded-lg bg-red-50
                                       text-red-600 transition hover:bg-red-100"
                                title="Hapus option"
                            >
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>

                        </div>

                    </div>

                    {{-- Preview child --}}
                    @if ($hasChild)
                        <div class="mt-4 border-t border-gray-200 pt-4 md:ml-12">

                            @if ($option->answer_text2)
                                <label class="mb-2 block text-xs font-medium text-gray-600">
                                    {{ $option->answer_text2 }}
                                </label>
                            @endif

                            <textarea
                                rows="3"
                                disabled
                                class="w-full resize-none rounded-lg
                                       border border-gray-300 bg-white
                                       p-3 text-sm text-gray-700"
                                placeholder="{{ $childLabel }}"
                            ></textarea>

                        </div>
                    @endif

                </div>

            @empty

                <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-6 text-center">

                    <div
                        class="mx-auto flex h-11 w-11 items-center
                               justify-center rounded-full bg-white
                               text-gray-400 shadow-sm"
                    >
                        <i class="fa-solid fa-ranking-star"></i>
                    </div>

                    <p class="mt-3 text-sm font-semibold text-gray-600">
                        Belum ada option ranking
                    </p>

                    <p class="mt-1 text-xs text-gray-500">
                        Tambahkan minimal 5 option agar responden dapat
                        menentukan ranking 1 sampai 5.
                    </p>

                </div>

            @endforelse

        </div>

    </div>

</div>