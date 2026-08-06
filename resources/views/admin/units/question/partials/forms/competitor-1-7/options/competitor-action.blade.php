<div class="flex shrink-0 items-center gap-2">

    {{-- Edit --}}
    <button
        type="button"
        data-modal-open="editCompetitorModal"
        data-id="{{ $competitor->id }}"
        data-name="{{ $competitor->name }}"
        data-group-id="{{ $competitor->group_id }}"
        data-form-id="{{ $competitor->form_id }}"
        data-action="{{ route('competitor.update', [
            'id' => $competitor->id,
        ]) }}"
        class="inline-flex h-8 w-8 items-center justify-center
               rounded-lg bg-amber-100 text-amber-600
               transition hover:bg-amber-200"
        title="Edit kompetitor"
    >
        <i class="fa-solid fa-pen text-xs"></i>
    </button>

    {{-- Delete --}}
    <button
        type="button"
        data-modal-open="deleteCompetitorModal"
        data-id="{{ $competitor->id }}"
        data-name="{{ $competitor->name }}"
        data-group-id="{{ $competitor->group_id }}"
        data-form-id="{{ $competitor->form_id }}"
        data-action="{{ route('competitor.destroy', [
            'id' => $competitor->id,
        ]) }}"
        class="inline-flex h-8 w-8 items-center justify-center
               rounded-lg bg-red-100 text-red-600
               transition hover:bg-red-200"
        title="Hapus kompetitor"
    >
        <i class="fa-solid fa-trash text-xs"></i>
    </button>

</div>