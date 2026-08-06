<template id="createCprofileRowTemplate">

    <tr class="create-cprofile-row">

        {{-- NO --}}
        <td class="border-r border-gray-200 p-3 text-center">

            <span
                data-row-number
                class="text-sm font-medium text-gray-700"
            ></span>

        </td>


        {{-- PERTANYAAN GROUP --}}
        <td class="border-r border-gray-200 p-3">

            <input
                type="text"
                name="pertanyaan_group[]"
                required
                autocomplete="off"
                placeholder="Masukkan pertanyaan group"
                class="w-full rounded-lg border border-gray-300 px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-green-500"
            >

        </td>


        {{-- PERTANYAAN UNIT --}}
        <td class="border-r border-gray-200 p-3">

            <input
                type="text"
                name="pertanyaan_unit[]"
                required
                autocomplete="off"
                placeholder="Masukkan pertanyaan unit"
                class="w-full rounded-lg border border-gray-300 px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-green-500"
            >

        </td>


        {{-- ACTION --}}
        <td class="p-3 text-center">

            <button
                type="button"
                data-remove-row
                class="text-red-500 transition hover:text-red-700"
                title="Hapus baris"
            >
                <i class="fa-solid fa-trash"></i>
            </button>

        </td>

    </tr>

</template>
