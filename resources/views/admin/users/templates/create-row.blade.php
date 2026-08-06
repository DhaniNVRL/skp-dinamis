<template id="createUserRowTemplate">

    <tr class="create-user-row">

        {{-- NO --}}
        <td class="border-r border-gray-200 p-3 text-center">

            <span
                data-row-number
                class="text-sm font-medium text-gray-700"
            ></span>

        </td>


        {{-- USERNAME --}}
        <td class="border-r border-gray-200 p-3">

            <input
                type="text"
                name="username[]"
                required
                autocomplete="off"
                placeholder="Username"
                class="w-full rounded-lg border border-gray-300 px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-green-500"
            >

        </td>


        {{-- PASSWORD --}}
        <td class="border-r border-gray-200 p-3">

            <input
                type="text"
                name="password[]"
                required
                autocomplete="off"
                placeholder="Password"
                class="w-full rounded-lg border border-gray-300 px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-green-500"
            >

        </td>


        {{-- ROLE --}}
        <td class="border-r border-gray-200 p-3">

            <select
                name="role_id[]"
                required
                class="role-select w-full rounded-lg border border-gray-300 bg-white px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-green-500"
            >

                <option value="">
                    Pilih Role
                </option>

                @foreach($roles as $role)

                    <option value="{{ $role->id }}">
                        {{ $role->name }}
                    </option>

                @endforeach

            </select>

        </td>


        {{-- ACTIVITY --}}
        <td class="activity-column border-r border-gray-200 p-3">

            <select
                name="activity_id[]"
                required
                class="activity-select w-full rounded-lg border border-gray-300 bg-white px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-green-500"
            >

                <option value="">
                    Pilih Activity
                </option>

                @foreach($activities as $activity)

                    <option value="{{ $activity->id }}">
                        {{ $activity->name }}
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