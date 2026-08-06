<div
    id="editFormModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4"
>
    <div class="w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-xl">

        {{-- Header --}}
        <div class="flex items-start justify-between border-b border-gray-200 px-6 py-4">

            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Edit Form
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Perbarui nomor urut, nama, dan tipe form.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="editFormModal"
                class="flex h-9 w-9 items-center justify-center
                       rounded-lg text-gray-500 transition hover:bg-gray-100"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>

        <form
            id="editFormForm"
            method="POST"
            action=""
        >
            @csrf
            @method('PUT')

            <input
                type="hidden"
                id="edit_form_id"
            >

            <input
                type="hidden"
                id="edit_form_group_id"
                name="group_id"
            >

            {{-- Body --}}
            <div class="space-y-5 p-6">

                {{-- Nomor Urut --}}
                <div>
                    <label
                        for="edit_form_no_urut"
                        class="mb-2 block text-sm font-medium text-gray-700"
                    >
                        Nomor urut
                    </label>

                    <input
                        type="number"
                        id="edit_form_no_urut"
                        name="no_urut"
                        min="1"
                        required
                        placeholder="Contoh: 1"
                        class="w-full rounded-lg border border-gray-300
                               px-4 py-2.5 text-sm
                               focus:border-indigo-500 focus:outline-none
                               focus:ring-1 focus:ring-indigo-500"
                    >
                </div>

                {{-- Nama Form --}}
                <div>
                    <label
                        for="edit_form_name"
                        class="mb-2 block text-sm font-medium text-gray-700"
                    >
                        Nama form
                    </label>

                    <input
                        type="text"
                        id="edit_form_name"
                        name="name"
                        required
                        maxlength="255"
                        placeholder="Masukkan nama form"
                        class="w-full rounded-lg border border-gray-300
                               px-4 py-2.5 text-sm
                               focus:border-indigo-500 focus:outline-none
                               focus:ring-1 focus:ring-indigo-500"
                    >
                </div>

                {{-- Tipe Form --}}
                <div>
                    <label
                        for="edit_form_type"
                        class="mb-2 block text-sm font-medium text-gray-700"
                    >
                        Tipe form
                    </label>

                    <select
                        id="edit_form_type"
                        name="formtype_id"
                        required
                        class="w-full rounded-lg border border-gray-300
                               bg-white px-4 py-2.5 text-sm
                               focus:border-indigo-500 focus:outline-none
                               focus:ring-1 focus:ring-indigo-500"
                    >
                        <option value="">
                            Pilih tipe form
                        </option>

                        @foreach ($formTypes as $formType)
                            <option value="{{ $formType->id }}">
                                {{ $formType->name }} - 
                                {{ $formType->description }}
                            </option>
                        @endforeach
                    </select>

                    <div
                        id="edit_form_type_warning"
                        class="mt-3 hidden rounded-lg border border-amber-200
                               bg-amber-50 px-4 py-3"
                    >
                        <div class="flex items-start gap-2 text-sm text-amber-700">

                            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>

                            <p>
                                Mengubah tipe form dapat memengaruhi tampilan
                                dan struktur pertanyaan di dalam form.
                            </p>

                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4">

                <button
                    type="button"
                    data-modal-close="editFormModal"
                    class="rounded-lg border border-gray-300 px-4 py-2
                           text-sm font-medium text-gray-700
                           transition hover:bg-gray-50"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-lg
                           bg-indigo-600 px-4 py-2 text-sm font-medium
                           text-white transition hover:bg-indigo-700"
                >
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>
</div>