<template id="createSubUnitRowTemplate">
    <tr data-create-subunit-row>
        <td
            data-row-number
            class="px-4 py-3 text-center text-sm text-gray-600"
        ></td>

        <td class="px-4 py-3">
            <input
                type="text"
                data-subunit-name-input
                placeholder="Masukkan nama Sub Unit"
                maxlength="500"
                required
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
            >
        </td>

        <td class="px-4 py-3 text-center">
            <button
                type="button"
                data-remove-subunit-row
                class="text-red-500 transition hover:text-red-700"
                title="Hapus baris"
            >
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    </tr>
</template>