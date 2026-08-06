<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table id="activityTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="w-12 px-4 py-3 text-center">
                        <input type="checkbox" class="bulk-select-all rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </th>
                    <th class="w-20 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">No</th>
                    <th class="w-24 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">ID</th>
                    <th class="min-w-[220px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Activity</th>
                    <th class="min-w-[320px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Description</th>
                    <th class="w-36 px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($activities as $activity)
                    <tr
                        class="transition hover:bg-gray-50"
                        data-search="{{ strtolower($activity->id . ' ' . $activity->name . ' ' . $activity->description) }}"
                    >
                        <td class="px-4 py-3 text-center">
                            <input
                                form="bulkDeleteForm"
                                type="checkbox"
                                name="ids[]"
                                value="{{ $activity->id }}"
                                data-label="{{ $activity->name }}"
                                data-username="{{ $activity->name }}"
                                class="bulk-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            >
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $activity->id }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $activity->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $activity->description ?: '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            @include('admin.activities.partials.row-action', ['activity' => $activity])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500">
                            <i class="fa-regular fa-folder-open mb-2 block text-2xl text-gray-400"></i>
                            Belum ada data Activity.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
