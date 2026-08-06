<div class="flex items-center justify-center gap-3">
    <a
        href="{{ route('admin.groups', ['id' => $activity->id]) }}"
        class="text-sky-600 transition hover:text-sky-800"
        title="Buka Group"
    >
        <i class="fa-solid fa-arrow-right-long"></i>
    </a>

    <button
        type="button"
        data-modal-open="editActivityModal"
        data-id="{{ $activity->id }}"
        data-name="{{ $activity->name }}"
        data-description="{{ $activity->description }}"
        data-action="{{ route('activities.update', ['id' => $activity->id]) }}"
        class="text-amber-500 transition hover:text-amber-700"
        title="Edit Activity"
    >
        <i class="fa-solid fa-pen-to-square"></i>
    </button>

    <button
        type="button"
        data-modal-open="deleteModal"
        data-id="{{ $activity->id }}"
        data-name="{{ $activity->name }}"
        class="text-red-600 transition hover:text-red-800"
        title="Hapus Activity"
    >
        <i class="fa-solid fa-trash"></i>
    </button>
</div>
