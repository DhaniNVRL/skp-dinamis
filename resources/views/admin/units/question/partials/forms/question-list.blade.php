<div
    class="space-y-5"
    data-question-bulk-container
    data-form-id="{{ $form->id }}"
>

    @if ($questions->isNotEmpty())
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm">
            <label class="inline-flex cursor-pointer items-center gap-2 text-sm font-medium text-gray-700">
                <input
                    type="checkbox"
                    data-question-select-all
                    class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500"
                >
                Pilih semua pertanyaan
            </label>

            <div data-question-bulk-action class="hidden items-center gap-3">
                <span class="text-sm font-medium text-red-700">
                    <span data-question-selected-count>0</span> pertanyaan dipilih
                </span>

                <form
                    id="questionBulkDeleteForm-{{ $form->id }}"
                    action="{{ route('questions.bulk-delete') }}"
                    method="POST"
                    data-question-bulk-form
                >
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="form_id" value="{{ $form->id }}">

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                    >
                        <i class="fa-solid fa-trash"></i>
                        Hapus yang Dipilih
                    </button>
                </form>
            </div>
        </div>
    @endif

    @forelse ($questions as $question)

        @include(
            'admin.units.question.partials.forms.question-card',
            [
                'form' => $form,
                'question' => $question,
                'competitors' => $competitors ?? collect(),
            ]
        )

    @empty

        <div class="rounded-xl border border-dashed border-gray-300 bg-white py-12 text-center">
            Belum ada pertanyaan
        </div>

    @endforelse

</div>
