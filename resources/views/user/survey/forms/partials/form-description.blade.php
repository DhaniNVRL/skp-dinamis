@php
    $descriptionContent =
        $form->description->content
        ?? $form->description->description
        ?? $form->description->text
        ?? null;
@endphp

@if (
    (int) $form->formtype_id !== 12 &&
    filled($descriptionContent)
)
    <div
        class="overflow-hidden rounded-xl
               border border-indigo-200
               bg-white shadow-sm"
    >
        <div
            class="flex items-center gap-3
                   border-b border-indigo-200
                   bg-indigo-50 px-6 py-4"
        >
            <span
                class="inline-flex h-10 w-10
                       shrink-0 items-center justify-center
                       rounded-lg bg-indigo-100
                       text-indigo-600"
            >
                <i class="fa-solid fa-circle-info"></i>
            </span>

            <div>
                <h2 class="font-semibold text-gray-900">
                    Petunjuk Pengisian
                </h2>

                <p class="text-sm text-gray-500">
                    Bacalah petunjuk sebelum mengisi pertanyaan.
                </p>
            </div>
        </div>

        <article
            class="prose max-w-none px-6 py-5
                   text-gray-700"
        >
            {!! $descriptionContent !!}
        </article>
    </div>
@endif