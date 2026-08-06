<template id="createRowFormTemplate">

    <tr class="create-form-row">

        {{-- NO URUT --}}
        <td class="border-r border-gray-200 p-3">

            <input
                type="number"
                min="1"
                required
                data-form-field="no_urut"
                placeholder="1"
                class="w-full rounded-lg border border-gray-300 px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-green-500"
            >

        </td>


        {{-- NAMA FORM --}}
        <td class="border-r border-gray-200 p-3">

            <input
                type="text"
                required
                autocomplete="off"
                data-form-field="name"
                placeholder="Nama form"
                class="w-full rounded-lg border border-gray-300 px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-green-500"
            >

        </td>


        {{-- TYPE FORM --}}
        <td class="border-r border-gray-200 p-3">

            <select
                required
                data-form-field="formtype_id"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-green-500"
            >

                <option value="">
                    Pilih Type Form
                </option>

                @foreach ($formTypes as $formType)

                    <option value="{{ $formType->id }}">
                        {{ $formType->name }} - 
                        {{ $formType->description }}
                    </option>

                @endforeach

            </select>

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