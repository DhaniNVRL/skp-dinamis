<div class="flex shrink-0 items-center gap-2">

    <label
        class="inline-flex h-9 w-9 cursor-pointer items-center justify-center rounded-lg border border-gray-300 bg-white hover:bg-gray-50"
        title="Pilih pertanyaan"
    >
        <input
            type="checkbox"
            name="ids[]"
            value="{{ $question->id }}"
            form="questionBulkDeleteForm-{{ $form->id }}"
            data-question-bulk-checkbox
            data-question-id="{{ $question->id }}"
            data-question-header="{{ $question->no_header }}"
            data-question-no="{{ $question->no }}"
            data-question-name="{{ $question->name }}"
            class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500"
            aria-label="Pilih pertanyaan {{ $question->name }}"
        >
    </label>

     <button
        type="button"
        data-modal-open="editQuestionModal"

        data-id="{{ $question->id }}"
        data-group-id="{{ $question->group_id }}"
        data-form-id="{{ $question->form_id }}"
        data-form-type-id="{{ $form->formtype_id }}"

        data-header="{{ $question->no_header }}"
        data-no="{{ $question->no }}"
        data-name="{{ $question->name }}"
        data-reason-prompt="{{ $question->reason_prompt }}"

        data-question-type-id="{{
            $question->questiontype_id
            ?? $question->id_questiontypes
        }}"
        data-scale-min="{{ $question->scale_min ?? 1 }}"
        data-scale-max="{{ $question->scale_max ?? 10 }}"
        data-scale-include-zero="{{ $question->scale_include_zero ? 1 : 0 }}"
        data-scale-child-values="{{ implode(',', $question->scale_child_values ?? []) }}"
        data-scale-left-label="{{ $question->scale_left_label }}"
        data-scale-right-label="{{ $question->scale_right_label }}"

        data-action="{{ route('question.update', $question->id) }}"

        class="inline-flex h-9 w-9 items-center justify-center
               rounded-lg bg-amber-100 text-amber-600
               transition hover:bg-amber-200"
        title="Edit pertanyaan"
    >
        <i class="fa-solid fa-pen"></i>
    </button>

    <button
        type="button"
        data-modal-open="deleteQuestionModal"
        data-id="{{ $question->id }}"
        data-name="{{ $question->name }}"
        data-option-count="{{ $question->options?->count() ?? 0 }}"
        data-action="{{ route('question.destroy', [
            'id' => $question->id,
        ]) }}"

        onclick="
            const deleteForm = document.getElementById('deleteQuestionForm');

            if (deleteForm) {
                deleteForm.action = this.dataset.action;
            }
        "

        class="flex h-8 w-8 items-center justify-center
            rounded-lg bg-red-50 text-red-600
            transition hover:bg-red-100"
        title="Hapus pertanyaan"
    >
        <i class="fa-solid fa-trash text-xs"></i>
    </button>

</div>
