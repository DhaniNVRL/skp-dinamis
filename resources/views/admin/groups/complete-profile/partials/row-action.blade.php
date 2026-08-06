<div class="flex items-center justify-center gap-3">
    <button type="button" data-modal-open="editCprofileModal" data-id="{{ $cprofile->id }}" data-pgroup="{{ $cprofile->group_question }}" data-punit="{{ $cprofile->unit_question }}" data-action="{{ route('cprofile.update', ['id' => $cprofile->id]) }}" class="text-amber-500 transition hover:text-amber-700" title="Edit Complete Profile"><i class="fa-solid fa-pen-to-square"></i></button>
    <button type="button" data-modal-open="deleteCprofileModal" data-id="{{ $cprofile->id }}" data-name="{{ $cprofile->group_question }}" data-action="{{ route('cprofile.destroy', ['id' => $cprofile->id]) }}" class="text-red-600 transition hover:text-red-800" title="Hapus Complete Profile"><i class="fa-solid fa-trash"></i></button>
</div>
