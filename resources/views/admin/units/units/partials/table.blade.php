<div
    class="overflow-hidden rounded-lg
        border border-gray-200 bg-white shadow-sm"
>
    <div class="overflow-x-auto">
        <table
            id="unitsTable"
            class="min-w-full divide-y divide-gray-200"
        >
            <thead class="bg-gray-100">
                <tr>
                    <th class="w-12 px-4 py-3 text-center">
                        <input
                            id="selectAllUnit"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300
                                text-blue-600 focus:ring-blue-500"
                        >
                    </th>

                    <th
                        class="w-20 px-4 py-3 text-left
                            text-xs font-semibold uppercase
                            tracking-wider text-gray-600"
                    >
                        No
                    </th>

                    <th
                        class="px-4 py-3 text-left
                            text-xs font-semibold uppercase
                            tracking-wider text-gray-600"
                    >
                        Nama Unit
                    </th>

                    <th
                        class="w-32 px-4 py-3 text-center
                            text-xs font-semibold uppercase
                            tracking-wider text-gray-600"
                    >
                        Aksi
                    </th>
                </tr>
            </thead>

            <tbody
                id="unitTableBody"
                class="divide-y divide-gray-100 bg-white"
            >
                @forelse ($units as $unit)
                    @include(
                        'admin.units.units.partials.row-action',
                        [
                            'unit' => $unit,
                            'number' => $loop->iteration,
                        ]
                    )
                @empty
                    <tr id="unitEmptyRow">
                        <td
                            colspan="4"
                            class="px-4 py-12 text-center text-sm text-gray-500"
                        >
                            <i
                                class="fa-solid fa-inbox
                                    mb-3 block text-3xl text-gray-300"
                            ></i>

                            Belum ada data Unit.
                        </td>
                    </tr>
                @endforelse

                <tr id="unitSearchEmptyRow" class="hidden">
                    <td
                        colspan="4"
                        class="px-4 py-12 text-center text-sm text-gray-500"
                    >
                        Data Unit tidak ditemukan.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>