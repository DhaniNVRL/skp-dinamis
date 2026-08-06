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
        Nomor telepon
    </label>

    <div class="flex overflow-hidden rounded-xl border border-gray-200 bg-gray-50">

        <div
            class="flex shrink-0 items-center gap-2
                   border-r border-gray-200 bg-gray-100
                   px-4 text-sm font-medium text-gray-600"
        >
            <i class="fa-solid fa-phone text-gray-400"></i>
            +62
        </div>

        <input
            type="tel"
            disabled
            placeholder="812 3456 7890"
            class="block w-full border-0 bg-transparent
                   px-4 py-3 text-sm text-gray-600
                   placeholder:text-gray-400
                   focus:ring-0 disabled:cursor-not-allowed"
        >

    </div>

    <p class="flex items-center gap-1.5 text-xs text-gray-400">
        <i class="fa-solid fa-circle-info"></i>
        Responden mengisi nomor telepon aktif.
    </p>

</div>