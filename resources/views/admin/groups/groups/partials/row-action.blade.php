<div class="flex items-center justify-center gap-3">
    <a href="{{ route('admin.units', ['id' => $group->id]) }}" class="text-sky-600 transition hover:text-sky-800" title="Buka Unit"><i class="fa-solid fa-arrow-right-long"></i></a>
    <button type="button" data-modal-open="editGroupModal" data-id="{{ $group->id }}" data-name="{{ $group->name }}" data-action="{{ route('groups.update', ['id' => $group->id]) }}" class="text-amber-500 transition hover:text-amber-700" title="Edit Group"><i class="fa-solid fa-pen-to-square"></i></button>
    <button type="button" data-modal-open="deleteGroupModal" data-id="{{ $group->id }}" data-name="{{ $group->name }}" data-action="{{ route('groups.destroy', ['id' => $group->id]) }}" class="text-red-600 transition hover:text-red-800" title="Hapus Group"><i class="fa-solid fa-trash"></i></button>
</div>
