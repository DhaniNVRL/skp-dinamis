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


    <label class="block text-xs font-medium uppercase tracking-wide text-gray-500">
        Jawaban singkat
    </label>

    <div class="relative">

        <div
            class="pointer-events-none absolute inset-y-0 left-0
                   flex items-center pl-4 text-gray-400"
        >
            <i class="fa-regular fa-pen-to-square"></i>
        </div>

        <input
            type="text"
            disabled
            placeholder="Responden menulis jawaban singkat..."
            class="block w-full rounded-xl border border-gray-200
                   bg-gray-50 py-3 pl-11 pr-4
                   text-sm text-gray-600
                   placeholder:text-gray-400
                   disabled:cursor-not-allowed"
        >

    </div>

</div>