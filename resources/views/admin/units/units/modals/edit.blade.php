<div
    id="editUnitModal"
    data-modal
    class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white rounded-lg w-full max-w-xl">

        <div class="border-b p-4 flex justify-between items-center">

            <h2 class="font-semibold text-lg">
                Edit Unit
            </h2>

            <button
                type="button"
                data-modal-close="editUnitModal">
                ✕
            </button>

        </div>

        <form
            id="editUnitForm"
            method="POST">

            @csrf
            @method('PUT')

            <input
                type="hidden"
                name="id"
                id="edit_unit_id">

            <div class="p-5 space-y-4">

                <div>
                    <label class="block mb-1 font-medium">
                        Nama Unit
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="edit_unit_name"
                        class="w-full border rounded p-2"
                        required>
                </div>

            </div>

            <div class="border-t p-4 flex justify-end gap-2">

                <button
                    type="button"
                    data-modal-close="editUnitModal"
                    class="border px-4 py-2 rounded">
                    Batal
                </button>

                <button
                    type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded">
                    Simpan
                </button>

            </div>

        </form>

    </div>
</div>