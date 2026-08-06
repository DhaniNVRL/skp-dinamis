<template id="createQuestionRowTemplate">

    <tr class="question-create-row">

        {{-- Header --}}
        <td class="px-4 py-3 align-top">
            <input
                type="text"
                data-question-field="no_header"
                maxlength="20"
                placeholder="A"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                       focus:border-indigo-500 focus:outline-none
                       focus:ring-1 focus:ring-indigo-500"
            >
        </td>

        {{-- Nomor --}}
        <td class="px-4 py-3 align-top">
            <input
                type="number"
                data-question-field="no"
                min="0"
                required
                placeholder="1"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                       focus:border-indigo-500 focus:outline-none
                       focus:ring-1 focus:ring-indigo-500"
            >
        </td>

        {{-- Pertanyaan --}}
        <td class="px-4 py-3 align-top">
            <textarea
                data-question-field="name"
                rows="2"
                required
                placeholder="Masukkan pertanyaan"
                class="w-full resize-none rounded-lg border border-gray-300
                       px-3 py-2 text-sm
                       focus:border-indigo-500 focus:outline-none
                       focus:ring-1 focus:ring-indigo-500"
            ></textarea>
        </td>

        {{-- Jenis pertanyaan --}}
        <td class="px-4 py-3 align-top">
            <select
                data-question-field="questiontype_id"
                data-question-type-select
                required
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                       focus:border-indigo-500 focus:outline-none
                       focus:ring-1 focus:ring-indigo-500"
            >
                <option value="">
                    Pilih tipe
                </option>
            </select>

            <p
                data-question-type-help
                class="mt-1 text-xs leading-5 text-gray-500"
            ></p>
        </td>

        {{-- Aksi --}}
        <td class="px-4 py-3 text-center align-top">
            <button
                type="button"
                data-remove-question-row
                class="inline-flex h-9 w-9 items-center justify-center
                       rounded-lg bg-red-50 text-red-600
                       transition hover:bg-red-100"
                title="Hapus baris"
            >
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>

    </tr>

</template>