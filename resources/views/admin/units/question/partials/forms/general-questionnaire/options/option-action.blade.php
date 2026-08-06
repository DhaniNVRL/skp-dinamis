<div class="flex shrink-0 items-center gap-2">
    {{-- Edit Option --}}
    <button
        type="button"
        data-modal-open="editOptionModal"
        data-id="{{ $option->id }}"
        data-question-id="{{ $question->id }}"
        data-answer-text="{{ $option->answer_text }}"
        data-answer-text2="{{ $option->answer_text2 }}"
        data-has-child="{{ $option->has_child }}"
        data-action="{{ route('options.update', $option->id) }}"
        class="flex h-8 w-8 items-center justify-center
               rounded-lg bg-amber-50 text-amber-600
               transition hover:bg-amber-100"
        title="Edit Option"
    >
        <i class="fa-solid fa-pen"></i>
    </button>


    {{-- Delete Option --}}
    <button
        type="button"
        data-modal-open="deleteOptionModal"
        data-id="{{ $option->id }}"
        data-name="{{ $option->answer_text }}"
        data-action="{{ route('options.destroy', $option->id) }}"
        class="flex h-8 w-8 items-center justify-center
               rounded-lg bg-red-50 text-red-600
               transition hover:bg-red-100"
        title="Hapus Option"
    >
        <i class="fa-solid fa-trash"></i>
    </button>

</div>