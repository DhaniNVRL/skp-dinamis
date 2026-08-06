<div class="space-y-6">

    @include(
        'admin.units.question.partials.forms.description',
        [
            'form' => $form,
        ]
    )

    @include(
        'admin.units.question.partials.forms.question-list',
        [
            'form' => $form,
        ]
    )

    {{-- Daftar kompetitor --}}
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">

            <div>
                <h4 class="font-semibold text-gray-800">
                    Daftar Kompetitor
                </h4>

                <p class="mt-1 text-xs text-gray-500">
                    Kelola kompetitor yang digunakan pada penilaian.
                </p>
            </div>

            <button
                type="button"
                data-modal-open="createCompetitorModal"
                data-group-id="{{ $form->group_id }}"
                data-form-id="{{ $form->id }}"
                data-action="{{ route('competitor.store') }}"
                class="inline-flex items-center gap-2 rounded-lg
                       bg-violet-600 px-4 py-2 text-sm font-medium
                       text-white hover:bg-violet-700"
            >
                <i class="fa-solid fa-plus"></i>
                Tambah Kompetitor
            </button>

        </div>

        <div class="flex flex-wrap gap-3">

            @forelse ($competitors as $competitor)

                <div
                    class="inline-flex items-center gap-3 rounded-lg
                           border border-gray-200 bg-gray-50 px-3 py-2"
                >
                    <span class="text-sm font-medium text-gray-700">
                        {{ $competitor->name }}
                    </span>

                    <form
                        action="{{ route('competitor.destroy', $competitor->id) }}"
                        method="POST"
                        onsubmit="return confirm('Hapus kompetitor ini?')"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="inline-flex h-7 w-7 items-center
                                   justify-center rounded-lg bg-red-100
                                   text-red-600 hover:bg-red-200"
                            title="Hapus kompetitor"
                        >
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>

            @empty

                <div class="w-full rounded-lg border border-dashed border-gray-300 p-5 text-center">
                    <p class="text-sm text-gray-500">
                        Belum ada kompetitor.
                    </p>
                </div>

            @endforelse

        </div>

    </div>

    @include(
        'admin.units.question.partials.forms.toolbar',
        [
            'form' => $form,
        ]
    )

</div>