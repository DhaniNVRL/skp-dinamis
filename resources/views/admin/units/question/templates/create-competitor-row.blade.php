<template id="createCompetitorRowTemplate">
    <tr class="competitor-create-row">
        <td class="px-4 py-3">
            <textarea
                data-competitor-field="name"
                rows="2"
                required
                placeholder="Masukkan nama kompetitor"
                class="w-full resize-none rounded-lg border border-gray-300
                       px-3 py-2 text-sm
                       focus:border-violet-500 focus:outline-none
                       focus:ring-1 focus:ring-violet-500"
            ></textarea>
        </td>
        <td class="px-4 py-3 text-center align-top">
            <button
                type="button"
                data-remove-competitor-row
                class="inline-flex h-9 w-9 items-center
                       justify-center rounded-lg bg-red-50
                       text-red-600 hover:bg-red-100"
            >
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    </tr>
</template>