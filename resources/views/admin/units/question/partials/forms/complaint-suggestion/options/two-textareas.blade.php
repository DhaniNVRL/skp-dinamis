<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

    {{-- Header pertanyaan --}}
    <div class="flex items-start justify-between gap-4 border-b border-gray-200 px-5 py-4">

        <div class="flex min-w-0 items-start gap-3">

            <span
                class="inline-flex min-w-12 shrink-0 items-center
                       justify-center rounded-lg bg-slate-100
                       px-2.5 py-1 text-sm font-semibold text-slate-700"
            >
                {{ $question->no_header }}{{ $question->no }}
            </span>

            <div class="min-w-0">

                <h4 class="font-semibold leading-6 text-gray-800">
                    {{ $question->name }}
                </h4>

                <p class="mt-1 text-xs text-gray-500">
                    Jawaban berupa Keluhan dan Saran.
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

    {{-- Preview jawaban --}}
    <div class="p-5">

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

            {{-- Keluhan --}}
            <div class="rounded-xl border border-red-200 bg-red-50/60 p-4">

                <div class="mb-3 flex items-center gap-2">

                    <span
                        class="inline-flex h-8 w-8 items-center
                               justify-center rounded-lg bg-red-100
                               text-red-600"
                    >
                        <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                    </span>

                    <div>
                        <h5 class="text-sm font-semibold text-red-800">
                            Keluhan
                        </h5>

                        <p class="text-xs text-red-600">
                            Kendala yang perlu mendapatkan perhatian
                        </p>
                    </div>

                </div>

                <textarea
                    rows="6"
                    disabled
                    class="w-full resize-none rounded-lg border
                           border-red-200 bg-white p-3
                           text-sm text-gray-700"
                    placeholder="Responden menuliskan kendala, hambatan, atau hal yang perlu mendapatkan perhatian..."
                ></textarea>

            </div>

            {{-- Saran --}}
            <div class="rounded-xl border border-blue-200 bg-blue-50/60 p-4">

                <div class="mb-3 flex items-center gap-2">

                    <span
                        class="inline-flex h-8 w-8 items-center
                               justify-center rounded-lg bg-blue-100
                               text-blue-600"
                    >
                        <i class="fa-solid fa-lightbulb text-sm"></i>
                    </span>

                    <div>
                        <h5 class="text-sm font-semibold text-blue-800">
                            Saran
                        </h5>

                        <p class="text-xs text-blue-600">
                            Masukan untuk peningkatan layanan
                        </p>
                    </div>

                </div>

                <textarea
                    rows="6"
                    disabled
                    class="w-full resize-none rounded-lg border
                           border-blue-200 bg-white p-3
                           text-sm text-gray-700"
                    placeholder="Responden memberikan ide, masukan, atau rekomendasi untuk peningkatan kualitas layanan..."
                ></textarea>

            </div>

        </div>

    </div>

</div>