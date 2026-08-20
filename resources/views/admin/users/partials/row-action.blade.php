<div class="flex items-center justify-center gap-3">
    @if ($profileUser->surveySession?->status === 'completed')
        <a
            href="{{ route('admin.datauser.answers.pdf', $profileUser->id) }}"
            class="text-red-600 transition hover:text-red-800"
            title="Download Review Jawaban PDF"
            aria-label="Download Review Jawaban PDF"
        >
            <i class="fa-solid fa-file-pdf"></i>
        </a>
    @endif

    <a
        href="{{ route('admin.datauser.answers', $profileUser->id) }}"
        class="text-blue-600 transition hover:text-blue-800"
        title="Lihat Jawaban ({{ $profileUser->answers_count ?? 0 }})"
    >
        <i class="fa-solid fa-clipboard-list"></i>
    </a>
    @if ($profileUser->surveySession?->status === 'completed')
        <button
            type="button"
            data-modal-open="reopenSurveyModal"
            data-name="{{ $profile->fullname ?: $profileUser->username }}"
            data-action="{{ route('admin.datauser.reopen-survey', $profileUser->id) }}"
            class="text-emerald-600 transition hover:text-emerald-800"
            title="Buka Kembali Akses Survey"
            aria-label="Buka Kembali Akses Survey"
        >
            <i class="fa-solid fa-lock-open"></i>
        </button>
    @endif

    @if (($profileUser->answers_count ?? 0) > 0)
        <button
            type="button"
            data-modal-open="deleteAnswersModal"
            data-name="{{ $profile->fullname ?: $profileUser->username }}"
            data-action="{{ route('admin.datauser.delete-answers', $profileUser->id) }}"
            class="text-rose-600 transition hover:text-rose-800"
            title="Hapus Semua Jawaban User"
            aria-label="Hapus Semua Jawaban User"
        >
            <i class="fa-solid fa-eraser"></i>
        </button>
    @endif

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
        class="text-orange-600 transition hover:text-orange-800"
        title="Reset Profile dan Hapus Jawaban"
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
