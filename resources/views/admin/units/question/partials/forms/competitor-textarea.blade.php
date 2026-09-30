<div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="flex items-start justify-between gap-4 border-b border-gray-200 px-5 py-4">
        <div class="flex min-w-0 items-start gap-3">
            <span class="inline-flex min-w-10 shrink-0 items-center justify-center rounded-lg bg-violet-100 px-2.5 py-1 text-sm font-semibold text-violet-700">
                {{ $question->no_header }}{{ $question->no }}
            </span>

            <div>
                <h4 class="font-semibold leading-6 text-gray-800">{{ $question->name }}</h4>
                <p class="mt-1 text-xs text-gray-500">Isian nama kompetitor</p>
            </div>
        </div>

        @include(
            'admin.units.question.partials.forms.question-action',
            compact('question', 'form')
        )
    </div>

    <div class="p-5">
        <textarea
            rows="3"
            disabled
            placeholder="Sebutkan nama kompetitor..."
            class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-500"
        ></textarea>
    </div>
</div>
