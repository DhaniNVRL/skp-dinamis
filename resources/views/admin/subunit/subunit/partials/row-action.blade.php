<div class="flex items-center justify-center gap-3">
    {{-- EDIT --}}
    <button
        type="button"
        data-modal-open="editSubUnitModal"
        data-id="{{ $subunit->id }}"
        data-name="{{ $subunit->name }}"
        data-action="{{ route('subunits.update', [
            'id' => $subunit->id,
        ]) }}"
        class="text-amber-500 transition hover:text-amber-700"
        title="Edit Sub Unit"
    >
        <i class="fa-solid fa-pen"></i>
    </button>

    {{-- DELETE --}}
    <button
        type="button"
        data-modal-open="deleteSubUnitModal"
        data-id="{{ $subunit->id }}"
        data-name="{{ $subunit->name }}"
        data-action="{{ route('subunits.destroy', [
            'id' => $subunit->id,
        ]) }}"
        class="text-red-500 transition hover:text-red-700"
        title="Hapus Sub Unit"
    >
        <i class="fa-solid fa-trash"></i>
    </button>
</div>