@php
    $descriptionContent = $form->description?->content
        ?? $form->description?->description
        ?? $form->description?->text
        ?? null;

    $showDescriptionHeader = (int) ($formTypeId ?? $form->formtype_id ?? 0) !== 12;
@endphp

@if (filled($descriptionContent))
    <section class="overflow-hidden rounded-xl border border-blue-200 bg-white">
        @if ($showDescriptionHeader)
            <div class="flex items-center gap-3 border-b border-blue-200 bg-blue-50 px-5 py-4">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                    <i class="fa-solid fa-circle-info"></i>
                </span>
                <div>
                    <h3 class="font-semibold text-gray-800">Petunjuk Pengisian</h3>
                    <p class="text-sm text-gray-500">Bacalah petunjuk sebelum melihat pertanyaan.</p>
                </div>
            </div>
        @endif

        <div class="prose max-w-none px-5 py-5 text-sm leading-relaxed text-gray-700 md:px-6">
            {!! $descriptionContent !!}
        </div>
    </section>
@endif