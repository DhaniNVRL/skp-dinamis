<template id="createOptionRowTemplate">

    <tr class="option-create-row">

        {{-- No --}}
        <td class="px-4 py-3 align-top">

            <input
                type="number"
                min="1"
                required
                data-option-field="no"
                placeholder="1"
                class="w-full rounded-lg border border-gray-300
                       px-3 py-2 text-sm
                       focus:border-indigo-500 focus:outline-none
                       focus:ring-1 focus:ring-indigo-500"
            >

        </td>


        {{-- Option --}}
        <td class="px-4 py-3 align-top">

            <textarea
                required
                rows="2"
                data-option-field="answer_text"
                placeholder="Masukkan option"
                class="w-full resize-none rounded-lg
                       border border-gray-300 px-3 py-2 text-sm
                       focus:border-indigo-500 focus:outline-none
                       focus:ring-1 focus:ring-indigo-500"
            ></textarea>

        </td>


        {{-- Has child --}}
        <td class="px-4 py-3 text-center align-top">

            <label
                class="inline-flex cursor-pointer items-center gap-2"
            >
                <input
                    type="checkbox"
                    value="1"
                    data-option-child-toggle
                    data-option-field="has_child"
                    class="h-4 w-4 rounded border-gray-300
                           text-indigo-600 focus:ring-indigo-500"
                >

                <span class="text-sm text-gray-600">
                    Ada
                </span>
            </label>

            <input
                type="hidden"
                value="0"
                data-option-child-hidden
            >

        </td>


        {{-- Label child --}}
        <td class="px-4 py-3 align-top">

            <input
                type="text"
                disabled
                data-option-field="answer_text2"
                placeholder="Contoh: Jelaskan alasan Anda"
                class="option-child-label w-full rounded-lg
                       border border-gray-300 bg-gray-100
                       px-3 py-2 text-sm text-gray-500
                       disabled:cursor-not-allowed"
            >

            <p class="mt-1 text-xs text-gray-400">
                Akan tampil sebagai label jawaban tambahan.
            </p>

        </td>


        {{-- Delete row --}}
        <td class="px-4 py-3 text-center align-top">

            <button
                type="button"
                data-remove-option-row
                class="flex h-9 w-9 items-center justify-center
                       rounded-lg bg-red-50 text-red-600
                       hover:bg-red-100"
                title="Hapus baris"
            >
                <i class="fa-solid fa-trash"></i>
            </button>

        </td>

    </tr>

</template>