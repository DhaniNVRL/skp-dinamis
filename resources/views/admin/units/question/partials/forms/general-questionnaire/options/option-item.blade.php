<div
    class="group rounded-xl border border-gray-200
           bg-white px-4 py-3
           transition hover:border-indigo-200 hover:shadow-sm"
>

    <div class="flex items-center gap-3">

        {{-- Preview input --}}
        <div class="flex shrink-0 items-center">

            @if ($inputType === 'radio')

                <span
                    class="flex h-5 w-5 items-center justify-center
                           rounded-full border-2 border-gray-300
                           bg-white"
                >
                    <span class="h-2 w-2 rounded-full bg-transparent"></span>
                </span>

            @elseif ($inputType === 'checkbox')

                <span
                    class="flex h-5 w-5 items-center justify-center
                        rounded-md border-2 border-gray-300 bg-white"
                ></span>

            @elseif ($inputType === 'dropdown')

                <span
                    class="flex h-6 min-w-6 items-center justify-center
                        rounded-md bg-cyan-50 px-1.5
                        text-xs font-semibold text-cyan-700"
                >
                    {{ $loop->iteration ?? $option->no }}
                </span>

            @endif

        </div>


        {{-- Isi option --}}
        <div class="min-w-0 flex-1">

            <div class="flex items-center gap-2">

                @if (!empty($option->no))

                    <span
                        class="inline-flex h-6 min-w-6 items-center
                               justify-center rounded-md bg-gray-100
                               px-1.5 text-xs font-semibold text-gray-600"
                    >
                        {{ $option->no }}
                    </span>

                @endif

                <p class="break-words text-sm font-medium text-gray-700">
                    {{ $option->answer_text }}
                </p>

            </div>


            @if (!empty($option->answer_text2))

                <p class="mt-1 text-xs text-gray-500">
                    {{ $option->answer_text2 }}
                </p>

            @endif

        </div>


        {{-- Status child answer --}}
        @if ((int) ($option->has_child ?? 0) === 1)

            <span
                class="hidden shrink-0 items-center gap-1
                       rounded-full bg-emerald-50 px-2.5 py-1
                       text-xs font-medium text-emerald-700
                       sm:inline-flex"
            >
                <i class="fa-solid fa-turn-down text-[10px]"></i>
                Jawaban lanjutan
            </span>

        @endif


        {{-- Action --}}
        <div
            class="flex shrink-0 items-center gap-1
                   opacity-100 transition sm:opacity-0
                   sm:group-hover:opacity-100"
        >

            <button
                type="button"
                data-modal-open="editOptionModal"
                data-id="{{ $option->id }}"
                data-question-id="{{ $question->id }}"
                data-no="{{ $option->no }}"
                data-answer-text="{{ $option->answer_text }}"
                data-answer-text2="{{ $option->answer_text2 ?? '' }}"
                data-has-child="{{ (int) $option->has_child }}"
                data-action="{{ route('options.update', ['id' => $option->id]) }}"
                class="flex h-8 w-8 items-center justify-center
                    rounded-lg bg-amber-50 text-amber-600
                    transition hover:bg-amber-100"
                title="Edit option"
            >
                <i class="fa-solid fa-pen text-xs"></i>
            </button>

            <button
                type="button"
                data-modal-open="deleteOptionModal"
                data-id="{{ $option->id }}"
                data-name="{{ $option->answer_text }}"
                data-action="{{ route('options.destroy', ['id' => $option->id]) }}"
                class="flex h-8 w-8 items-center justify-center
                       rounded-lg bg-red-50 text-red-600
                       transition hover:bg-red-100"
                title="Hapus option"
            >
                <i class="fa-solid fa-trash text-xs"></i>
            </button>

        </div>

    </div>


    {{-- Preview jawaban tambahan --}}
    @if ((int) ($option->has_child ?? 0) === 1)

        <div class="ml-8 mt-3 border-t border-gray-100 pt-3">

            <div class="relative">

                <div
                    class="pointer-events-none absolute inset-y-0 left-0
                           flex items-center pl-3.5 text-gray-400"
                >
                    <i class="fa-regular fa-message text-xs"></i>
                </div>

                <input
                    type="text"
                    disabled
                    placeholder="{{ $option->answer_text2 ?: 'Jawaban tambahan responden...' }}"
                    class="w-full rounded-lg border border-gray-200
                           bg-gray-50 py-2.5 pl-9 pr-3
                           text-sm text-gray-500
                           placeholder:text-gray-400"
                >

            </div>

        </div>

    @endif

</div>