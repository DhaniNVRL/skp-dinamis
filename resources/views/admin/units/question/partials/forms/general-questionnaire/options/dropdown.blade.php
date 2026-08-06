<div class="space-y-2">
    <div class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-4">
        <!-- Nomor -->
        <span
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-sm font-semibold text-blue-700"
        >
            {{ $question->no_header }}{{ $question->no }}
        </span>

        <!-- Judul -->
        <div class="min-w-0 flex-1">
            <h4 class="truncate text-sm font-semibold text-gray-800">
                {{ $question->name }}
            </h4>
        </div>

        <!-- Action -->
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

    <div class="flex items-center justify-between gap-4">

        <div>
            <p class="text-sm font-medium text-gray-700">
                Pilihan dropdown
            </p>

            <p class="mt-0.5 text-xs text-gray-500">
                Responden memilih satu jawaban dari daftar.
            </p>
        </div>

        <span
            class="inline-flex shrink-0 items-center gap-1.5
                   rounded-full bg-cyan-50 px-3 py-1
                   text-xs font-medium text-cyan-700"
        >
            <i class="fa-solid fa-chevron-down"></i>
            Dropdown
        </span>

    </div>

    <div class="relative">

        <select
            disabled
            class="block w-full appearance-none rounded-xl
                   border border-gray-200 bg-gray-50
                   px-4 py-3 pr-11 text-sm text-gray-500
                   disabled:cursor-not-allowed"
        >
            <option>
                Pilih jawaban...
            </option>

            @foreach ($question->options as $option)

                <option value="{{ $option->id }}">
                    {{ $option->answer_text }}
                </option>

            @endforeach
        </select>

        <div
            class="pointer-events-none absolute inset-y-0 right-0
                   flex items-center pr-4 text-gray-400"
        >
            <i class="fa-solid fa-chevron-down text-xs"></i>
        </div>

    </div>

    <div class="flex items-center justify-between gap-3">

        <p class="text-xs text-gray-400">
            {{ $question->options->count() }} pilihan tersedia
        </p>

        <button
            type="button"
            data-modal-open="createOptionModal"
            data-question-id="{{ $question->id }}"
            data-action="{{ route('options.store') }}"
            class="inline-flex items-center gap-1.5
                   rounded-lg border border-dashed border-indigo-300
                   bg-indigo-50 px-3 py-1.5
                   text-xs font-medium text-indigo-700
                   hover:border-indigo-400 hover:bg-indigo-100"
        >
            <i class="fa-solid fa-plus"></i>
            Tambah Option
        </button>

    </div>

    @if ($question->options->isNotEmpty())

        <div class="space-y-2 pt-2">

            @foreach ($question->options as $option)

                @include(
                    'admin.units.question.partials.forms.general-questionnaire.options.option-item',
                    [
                        'question' => $question,
                        'option' => $option,
                        'inputType' => 'dropdown'
                    ]
                )

            @endforeach

        </div>

    @endif

</div>