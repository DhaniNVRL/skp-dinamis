@php
    $questionOptions = $question->options ?? collect();
@endphp

<div
    x-data="{ selectedOptions: [] }"
    class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
>

    <div class="flex items-start justify-between gap-4 border-b border-gray-200 px-5 py-4">

        <div class="flex min-w-0 items-start gap-3">
            <span
                class="inline-flex min-w-10 shrink-0 items-center justify-center
                       rounded-lg bg-blue-100 px-2.5 py-1
                       text-sm font-semibold text-blue-700"
            >
                {{ $question->no_header }}{{ $question->no }}
            </span>

            <div>
                <h4 class="font-semibold leading-6 text-gray-800">
                    {{ $question->name }}
                </h4>

                <p class="mt-1 text-xs text-gray-500">
                    Penilaian Kepentingan & Kinerja dengan pilihan alasan
                </p>
            </div>
        </div>

        <div class="flex shrink-0 items-center gap-2">
            @include(
                'admin.units.question.partials.forms.question-action',
                [
                    'question' => $question,
                    'form' => $form,
                ]
            )
        </div>

    </div>

    <div class="p-5">

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

            {{-- Kepentingan --}}
            <div class="rounded-xl border border-blue-200 bg-blue-50/50 p-4">
                <h5 class="mb-4 text-center font-semibold text-blue-800">
                    Kepentingan
                </h5>

                <div class="flex flex-wrap justify-center gap-3">
                    @foreach ([1, 2, 3, 4, 5, 0] as $value)
                        <span
                            class="inline-flex h-10 w-10 items-center justify-center
                                   rounded-full border border-blue-300 bg-white
                                   text-sm font-semibold text-blue-700"
                        >
                            {{ $value }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- Kinerja --}}
            <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4">
                <h5 class="mb-4 text-center font-semibold text-emerald-800">
                    Kinerja
                </h5>

                <div class="flex flex-wrap justify-center gap-3">
                    @foreach ([1, 2, 3, 4, 5, 0] as $value)
                        <span
                            class="inline-flex h-10 w-10 items-center justify-center
                                   rounded-full border border-emerald-300 bg-white
                                   text-sm font-semibold text-emerald-700"
                        >
                            {{ $value }}
                        </span>
                    @endforeach
                </div>
            </div>

        </div>
        
        {{-- Pilihan alasan --}}
        <div class="mt-5 rounded-xl border border-gray-200 bg-gray-50 p-4">

            {{-- Header pilihan alasan --}}
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">

                <div>
                    <h5 class="text-sm font-semibold text-gray-800">
                        Pilihan Alasan
                    </h5>

                    <p class="mt-1 text-xs text-gray-500">
                        Pilihan yang dapat dipilih sebagai alasan penilaian.
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
                        bg-indigo-600 px-3 py-2 text-xs font-medium
                        text-white transition hover:bg-indigo-700"
                >
                    <i class="fa-solid fa-plus"></i>
                    Tambah Pilihan Alasan
                </button>

            </div>

            {{-- Daftar pilihan alasan --}}
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
                            (int) $option->has_child === 1;
                    @endphp

                    <div class="rounded-lg border border-gray-200 bg-white p-3">

                        <div class="flex items-start justify-between gap-3">

                            {{-- Preview pilihan --}}
                            <div class="flex min-w-0 flex-1 items-start gap-3">

                                <input
                                    type="checkbox"
                                    disabled
                                    autocomplete="off"
                                    class="mt-1 rounded border-gray-300
                                        text-blue-600 opacity-70"
                                >

                                <div class="min-w-0 flex-1">

                                    <span class="block text-sm font-medium text-gray-700">
                                        {{ $optionLabel }}
                                    </span>

                                    @if ($hasChild)
                                        <span class="mt-1 inline-flex items-center gap-1 text-xs text-indigo-600">
                                            <i class="fa-solid fa-align-left"></i>
                                            Memiliki isian lanjutan
                                        </span>
                                    @endif

                                </div>

                            </div>

                            {{-- Action --}}
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
                                    class="inline-flex h-8 w-8 items-center justify-center
                                        rounded-lg bg-amber-50 text-amber-600
                                        transition hover:bg-amber-100"
                                    title="Edit pilihan"
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
                                    class="inline-flex h-8 w-8 items-center justify-center
                                        rounded-lg bg-red-50 text-red-600
                                        transition hover:bg-red-100"
                                    title="Hapus pilihan"
                                >
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>

                            </div>

                        </div>

                        {{-- Preview input lanjutan --}}
                        @if ($hasChild)
                            <div class="mt-3 pl-7">

                                @if ($option->answer_text2)
                                    <label class="mb-2 block text-xs font-medium text-gray-600">
                                        {{ $option->answer_text2 }}
                                    </label>
                                @endif

                                <textarea
                                    rows="3"
                                    disabled
                                    class="w-full resize-none rounded-lg
                                        border border-gray-300 bg-gray-50
                                        p-3 text-sm text-gray-700"
                                    placeholder="{{ $childLabel }}"
                                ></textarea>

                            </div>
                        @endif

                    </div>

                @empty

                    <div class="rounded-lg border border-dashed border-gray-300 bg-white p-5 text-center">
                        <p class="text-sm font-medium text-gray-600">
                            Belum ada pilihan alasan
                        </p>

                        <p class="mt-1 text-xs text-gray-500">
                            Tekan tombol Tambah Pilihan Alasan untuk memasukkan option.
                        </p>
                    </div>

                @endforelse

            </div>

        </div>

    </div>
</div>