<template id="createActivityRowTemplate">

    <tr class="create-activity-row">

        {{-- NO --}}
        <td class="border-r border-gray-200 p-3 text-center">

            <span
                data-row-number
                class="text-sm font-medium text-gray-700"
            ></span>

        </td>


        {{-- ACTIVITY NAME --}}
        <td class="border-r border-gray-200 p-3">

            <input
                type="text"
                name="name[]"
                required
                autocomplete="off"
                placeholder="Masukkan nama Activity"
                class="w-full rounded-lg border border-gray-300 px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-green-500"
            >

        </td>


        {{-- DESCRIPTION --}}
        <td class="border-r border-gray-200 p-3">

            <input
                type="text"
                name="description[]"
                required
                autocomplete="off"
                placeholder="Masukkan deskripsi Activity"
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
