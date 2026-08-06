<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="flex flex-col justify-between gap-4 border-b border-gray-200 bg-gray-50 p-6 md:flex-row md:items-start">
        <div class="flex items-start gap-4">
            <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-100 font-bold text-indigo-700">
                {{ $currentPosition }}
            </span>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $form->name }}</h1>
                <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
                    <span class="rounded-full bg-indigo-100 px-3 py-1 text-indigo-700">
                        <i class="fa-solid fa-clipboard-list mr-1"></i>
                        {{ $form->formtype->name ?? 'Form Survei' }}
                    </span>
                    @if (!empty($form->formtype?->description))
                        <span class="text-gray-500">{{ $form->formtype->description }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white px-5 py-3 text-center">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-400">Form</div>
            <div class="mt-1 font-bold text-indigo-600">{{ $currentPosition }} / {{ $totalForms }}</div>
        </div>
    </div>
    @include('user.survey.partials.progress')
</div>
