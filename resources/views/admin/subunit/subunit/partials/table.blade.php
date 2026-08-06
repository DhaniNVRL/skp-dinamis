<div class="overflow-visible rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table
            id="subUnitTable"
            class="min-w-full divide-y divide-gray-200"
        >
            <thead class="bg-gray-100">
                <tr>
                    <th class="w-12 px-4 py-3 text-center">
                        <input
                            id="selectAllSubUnit"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-blue-600"
                        >
                    </th>

                    <th class="w-20 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
                        No
                    </th>

                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                        Nama Sub Unit
                    </th>

                    <th class="w-36 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
                        Aksi
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($subunits as $subunit)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-4 py-3 text-center">
                            <input
                                type="checkbox"
                                value="{{ $subunit->id }}"
                                class="subunit-checkbox h-4 w-4 rounded border-gray-300 text-blue-600"
                            >
                        </td>

                        <td class="px-4 py-3 text-center text-sm text-gray-600">
                            {{ $subunits->firstItem() + $loop->index }}
                        </td>

                        <td class="px-4 py-3 text-sm font-medium text-gray-800">
                            {{ $subunit->name }}
                        </td>

                        <td class="px-4 py-3 text-center">
                            @include('admin.subunit.subunit.partials.row-action', [
                                'subunit' => $subunit,
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="4"
                            class="px-6 py-12 text-center"
                        >
                            <div class="text-4xl text-gray-300">
                                <i class="fa-solid fa-building-circle-xmark"></i>
                            </div>

                            <div class="mt-3 font-medium text-gray-600">
                                Sub Unit tidak ditemukan
                            </div>

                            <p class="mt-1 text-sm text-gray-400">
                                @if (request()->filled('search'))
                                    Tidak ada Sub Unit yang cocok dengan pencarian.
                                @else
                                    Silakan tambahkan atau import data Sub Unit.
                                @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>