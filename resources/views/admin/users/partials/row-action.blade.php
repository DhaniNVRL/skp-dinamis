<div class="flex items-center justify-center gap-3">
    <button
        type="button"
        data-modal-open="editUserModal"
        data-id="{{ $profileUser->id }}"
        data-username="{{ $profileUser->username }}"
        data-role="{{ $profileUser->role_id }}"
        data-activity="{{ $profile->activity_id }}"
        data-action="{{ route('admin.datauser.update', $profileUser->id) }}"
        class="text-amber-500 transition hover:text-amber-700"
        title="Edit User"
    >
        <i class="fa-solid fa-pen-to-square"></i>
    </button>

    <a href="{{ route('admin.datauser.editpassword', $profileUser->id) }}" class="text-indigo-600 transition hover:text-indigo-800" title="Ubah Password">
        <i class="fa-solid fa-key"></i>
    </a>

    <button
        type="button"
        data-modal-open="deleteModal"
        data-id="{{ $profileUser->id }}"
        data-name="{{ $profile->fullname ?: $profileUser->username }}"
        class="text-red-600 transition hover:text-red-800"
        title="Hapus User"
    >
        <i class="fa-solid fa-trash"></i>
    </button>
</div>
