<div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

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
                    Penilaian Kepentingan & Kinerja
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

    <div class="grid grid-cols-1 gap-5 p-5 lg:grid-cols-2">

        {{-- Kepentingan --}}
        <div class="rounded-xl border border-blue-200 bg-blue-50/50 p-4">
            <h5 class="mb-4 text-center font-semibold text-blue-800">
                Kepentingan
            </h5>

            <div class="flex flex-wrap justify-center gap-3">
                @foreach ([1, 2, 3, 4, 5, 0] as $value)
                    <label
                        class="inline-flex h-10 w-10 items-center justify-center
                               rounded-full border border-blue-300 bg-white
                               text-sm font-semibold text-blue-700"
                    >
                        <input
                            type="radio"
                            name="preview_kepentingan_{{ $question->id }}"
                            value="{{ $value }}"
                            class="sr-only"
                            disabled
                        >

                        {{ $value }}
                    </label>
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
                    <label
                        class="inline-flex h-10 w-10 items-center justify-center
                               rounded-full border border-emerald-300 bg-white
                               text-sm font-semibold text-emerald-700"
                    >
                        <input
                            type="radio"
                            name="preview_kinerja_{{ $question->id }}"
                            value="{{ $value }}"
                            class="sr-only"
                            disabled
                        >

                        {{ $value }}
                    </label>
                @endforeach
            </div>
        </div>

    </div>
</div>