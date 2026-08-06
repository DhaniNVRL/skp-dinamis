<div
    id="editCompetitorModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4"
>
    <div class="w-full max-w-xl overflow-hidden rounded-xl bg-white shadow-xl">

        {{-- Header --}}
        <div class="flex items-start justify-between border-b border-gray-200 px-6 py-4">

            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Edit Kompetitor
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Perbarui nama kompetitor atau perusahaan pembanding.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="editCompetitorModal"
                class="flex h-9 w-9 items-center justify-center
                       rounded-lg text-gray-500 hover:bg-gray-100"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>

        <form
            id="editCompetitorForm"
            method="POST"
        >
            @csrf
            @method('PUT')

            <input
                type="hidden"
                name="competitor_id"
                id="edit_competitor_id"
            >

            <input
                type="hidden"
                name="group_id"
                id="edit_competitor_group_id"
            >

            <input
                type="hidden"
                name="form_id"
                id="edit_competitor_form_id"
            >

            {{-- Body --}}
            <div class="p-6">

                <label
                    for="edit_competitor_name"
                    class="mb-2 block text-sm font-medium text-gray-700"
                >
                    Nama Kompetitor
                </label>

                <textarea
                    name="name"
                    id="edit_competitor_name"
                    rows="3"
                    required
                    maxlength="255"
                    placeholder="Masukkan nama kompetitor"
                    class="w-full resize-none rounded-lg border
                           border-gray-300 px-3 py-2 text-sm
                           focus:border-violet-500 focus:outline-none
                           focus:ring-1 focus:ring-violet-500"
                ></textarea>

            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4">

                <button
                    type="button"
                    data-modal-close="editCompetitorModal"
                    class="rounded-lg border border-gray-300
                           px-4 py-2 text-sm font-medium
                           text-gray-700 hover:bg-gray-50"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="rounded-lg bg-violet-600 px-4 py-2
                           text-sm font-medium text-white
                           hover:bg-violet-700"
                >
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>
</div>