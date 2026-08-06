<template id="createUnitRowTemplate">

    <tr
        data-create-unit-row
        class="unit-create-row"
    >

        {{-- NOMOR --}}
        <td
            class="border-r border-gray-200
                   px-4 py-3 text-center align-middle"
        >
            <span
                data-row-number
                class="text-sm font-medium text-gray-700"
            >
                1
            </span>
        </td>

        {{-- NAMA UNIT --}}
        <td
            class="border-r border-gray-200
                   px-4 py-3 align-middle"
        >
            <input
                type="text"
                name="name[]"
                data-unit-name-input
                required
                autocomplete="off"
                placeholder="Masukkan nama unit"
                class="w-full rounded-lg border border-gray-300
                       bg-white px-3 py-2 text-sm text-gray-800
                       outline-none transition
                       placeholder:text-gray-400
                       focus:border-blue-500
                       focus:ring-2 focus:ring-blue-200"
            >
        </td>

        {{-- ACTION --}}
        <td class="px-4 py-3 text-center align-middle">
            <button
                type="button"
                data-remove-unit-row
                class="inline-flex h-9 w-9 items-center
                       justify-center rounded-lg
                       text-red-500 transition
                       hover:bg-red-50 hover:text-red-700"
                title="Hapus baris"
            >
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>

    </tr>

</template>