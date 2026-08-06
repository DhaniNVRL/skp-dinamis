<div class="flex flex-wrap items-center gap-3 border-t border-gray-200 pt-5">

    {{-- ACTION PERTANYAAN --}}
    @if ((int) $form->formtype_id !== 12)

        <button
            type="button"
            data-modal-open="createQuestionModal"
            data-group-id="{{ $form->group_id }}"
            data-form-id="{{ $form->id }}"
            data-form-name="{{ $form->name }}"
            data-form-type-id="{{ $form->formtype_id }}"
            data-action="{{ route('question.store') }}"
            class="inline-flex items-center gap-2 rounded-lg
                   bg-green-600 px-4 py-2 text-sm font-medium
                   text-white transition hover:bg-green-700"
        >
            <i class="fa-solid fa-plus"></i>
            Tambah Pertanyaan
        </button>

        <a
            href="{{ route('question.template', [
                'formId' => $form->id,
            ]) }}"
            class="inline-flex items-center gap-2 rounded-lg
                   bg-emerald-600 px-4 py-2 text-sm font-medium
                   text-white transition hover:bg-emerald-700"
        >
            <i class="fa-solid fa-file-excel"></i>
            Download Template
        </a>

        <button
            type="button"
            data-modal-open="importQuestionModal"
            data-group-id="{{ $form->group_id }}"
            data-form-id="{{ $form->id }}"
            data-form-name="{{ $form->name }}"
            data-form-type-id="{{ $form->formtype_id }}"
            data-action="{{ route('question.import', [
                'formId' => $form->id,
            ]) }}"
            class="inline-flex items-center gap-2 rounded-lg
                bg-blue-600 px-4 py-2 text-sm font-medium
                text-white transition hover:bg-blue-700"
        >
            <i class="fa-solid fa-file-import"></i>
            Import Pertanyaan
        </button>

    @endif


    {{-- ACTION KOMPETITOR --}}
    @if ((int) $form->formtype_id === 11)

        <button
            type="button"
            data-modal-open="createCompetitorModal"
            data-group-id="{{ $form->group_id }}"
            data-form-id="{{ $form->id }}"
            data-form-name="{{ $form->name }}"
            data-action="{{ route('competitor.store') }}"
            class="inline-flex items-center gap-2 rounded-lg
                   bg-violet-600 px-4 py-2 text-sm font-medium
                   text-white transition hover:bg-violet-700"
        >
            <i class="fa-solid fa-building-circle-check"></i>
            Tambah Kompetitor
        </button>

    @endif


    {{-- ACTION DESCRIPTION --}}
    @if (!$form->description)

        <button
            type="button"
            data-modal-open="createDescriptionModal"
            data-group-id="{{ $form->group_id }}"
            data-form-id="{{ $form->id }}"
            data-form-name="{{ $form->name }}"
            class="inline-flex items-center gap-2 rounded-lg
                   bg-emerald-600 px-4 py-2 text-sm font-medium
                   text-white transition hover:bg-emerald-700"
        >
            <i class="fa-solid fa-file-lines"></i>
            Tambah Description
        </button>

    @endif

</div>