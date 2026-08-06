<div class="mb-6">
   @if ($form->description)

        <template id="descriptionContent{{ $form->description->id }}">
            {!! $form->description->content !!}
        </template>

        <div
            class="relative rounded-xl border border-gray-200
                bg-white p-6 shadow-sm"
        >
            <div class="absolute right-3 top-3 flex items-center gap-2">

                <button
                    type="button"
                    data-modal-open="editDescriptionModal"
                    data-id="{{ $form->description->id }}"
                    data-form-name="{{ $form->name }}"
                    data-content-template="descriptionContent{{ $form->description->id }}"
                    data-action="{{ route(
                        'description.update',
                        [
                            'id' => $form->description->id,
                        ]
                    ) }}"
                    class="flex h-8 w-8 items-center justify-center
                        rounded-full bg-amber-100 text-amber-600
                        transition hover:bg-amber-200"
                    title="Edit description"
                >
                    <i class="fa-solid fa-pen text-sm"></i>
                </button>

                <button
                    type="button"
                    data-modal-open="deleteDescriptionModal"
                    data-id="{{ $form->description->id }}"
                    data-form-name="{{ $form->name }}"
                    data-action="{{ route(
                        'description.destroy',
                        [
                            'id' => $form->description->id,
                        ]
                    ) }}"
                    class="flex h-8 w-8 items-center justify-center
                        rounded-full bg-red-100 text-red-600
                        transition hover:bg-red-200"
                    title="Hapus description"
                >
                    <i class="fa-solid fa-trash text-sm"></i>
                </button>

            </div>

            <div
                class="description-content prose max-w-none
                    overflow-x-auto pr-20"
            >
                {!! $form->description->content !!}
            </div>
        </div>

    @endif

</div>