<div class="flex items-center gap-3 shrink-0">

    {{-- EDIT --}}
    <button
        type="button"
        data-modal-open="editFormModal"
        data-id="{{ $form->id }}"
        data-group-id="{{ $form->group_id }}"
        data-no-urut="{{ $form->no_urut }}"
        data-name="{{ $form->name }}"
        data-formtype-id="{{ $form->formtype_id }}"
        data-action="{{ route('forms.update', ['id' => $form->id]) }}"
        class="flex h-8 w-8 items-center justify-center
            rounded-lg bg-amber-50 text-amber-600
            transition hover:bg-amber-100"
        title="Edit form"
    >
        <i class="fa-solid fa-pen text-xs"></i>
    </button>


    {{-- DELETE --}}
    <button
        type="button"
        data-modal-open="deleteFormModal"
        data-id="{{ $form->id }}"
        data-name="{{ $form->name }}"
        data-question-count="{{ $form->questions->count() }}"
        data-option-count="{{ $form->questions->sum(fn ($question) => $question->options->count()) }}"
        data-action="{{ route('forms.destroy', ['id' => $form->id]) }}"
        class="flex h-8 w-8 items-center justify-center
            rounded-lg bg-red-50 text-red-600
            transition hover:bg-red-100"
        title="Hapus form"
    >
        <i class="fa-solid fa-trash text-xs"></i>
    </button>

</div>