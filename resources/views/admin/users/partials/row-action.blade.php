<div class="flex items-center justify-center gap-3">
    <a
        href="{{ route('admin.datauser.answers', $profileUser->id) }}"
        class="text-blue-600 transition hover:text-blue-800"
        title="Lihat Jawaban ({{ $profileUser->answers_count ?? 0 }})"
    >
        <i class="fa-solid fa-clipboard-list"></i>
    </a>

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
        data-modal-open="resetProfileModal"
        data-name="{{ $profile->fullname ?: $profileUser->username }}"
        data-action="{{ route('admin.datauser.resetaccount', $profileUser->id) }}"
        class="text-cyan-600 transition hover:text-cyan-800"
        title="Reset Account: Hapus Jawaban, Profil Survey, dan Progres"
    >
        <i class="fa-solid fa-user-rotate"></i>
    </button>

    <button
        type="button"
        data-modal-open="deleteUserModal"
        data-id="{{ $profileUser->id }}"
        data-name="{{ $profile->fullname ?: $profileUser->username }}"
        data-action="{{ route('admin.datauser.destroy', $profileUser->id) }}"
        class="text-red-600 transition hover:text-red-800"
        title="Hapus User"
    >
        <i class="fa-solid fa-trash"></i>
    </button>
</div>
