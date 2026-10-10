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
                type="text"
                data-question-field="no"
                maxlength="20"
                required
                placeholder="Contoh: 1 atau L1A"
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

            <div data-scale-config class="mt-3 hidden space-y-2 rounded-lg border border-violet-200 bg-violet-50 p-3">
                <div class="grid grid-cols-2 gap-2">
                    <label class="text-xs text-gray-600">Nilai awal
                        <input type="number" min="0" max="999" value="1" data-question-field="scale_min" class="mt-1 w-full rounded border border-gray-300 px-2 py-1.5 text-sm">
                    </label>
                    <label class="text-xs text-gray-600">Nilai akhir
                        <input type="number" min="1" max="1000" value="10" data-question-field="scale_max" class="mt-1 w-full rounded border border-gray-300 px-2 py-1.5 text-sm">
                    </label>
                </div>
                <input type="text" maxlength="255" placeholder="Label kiri, contoh: Tidak Bersedia" data-question-field="scale_left_label" class="w-full rounded border border-gray-300 px-2 py-1.5 text-sm">
                <input type="text" maxlength="255" placeholder="Label kanan, contoh: Bersedia" data-question-field="scale_right_label" class="w-full rounded border border-gray-300 px-2 py-1.5 text-sm">
                <label class="flex items-center gap-2 text-xs font-medium text-violet-800">
                    <input type="checkbox" value="1" data-question-field="scale_include_zero" class="rounded border-violet-300 text-violet-600 focus:ring-violet-500">
                    Sertakan nilai 0 di paling kanan (0)
                </label>
                <label class="block text-xs font-medium text-violet-800">
                    Nilai pemicu textarea
                    <input type="text" data-question-field="scale_child_values" placeholder="Contoh: 1, 2, 3" class="mt-1 w-full rounded border border-violet-300 bg-white px-2 py-1.5 text-sm text-gray-700">
                    <span class="mt-1 block font-normal text-violet-600">Pisahkan beberapa nilai dengan koma. Kosongkan jika tidak memerlukan textarea.</span>
                </label>
            </div>

            <div data-reason-prompt-config class="mt-3 hidden rounded-lg border border-amber-200 bg-amber-50 p-3">
                <label class="text-xs font-medium text-amber-800">Narasi Alasan
                    <textarea rows="3" maxlength="5000" data-question-field="reason_prompt" placeholder="Contoh: Alasan apa yang membuat Bapak/Ibu memberikan penilaian kinerja tersebut?" class="mt-1 w-full rounded border border-gray-300 px-2 py-1.5 text-sm text-gray-700"></textarea>
                </label>
                <p class="mt-1 text-xs text-amber-700">Ditampilkan di atas textarea alasan responden.</p>
            </div>
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
