<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table id="GroupsTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="w-12 px-4 py-3 text-center"><input type="checkbox" class="bulk-select-all rounded border-gray-300 text-blue-600" id="selectAllTable"></th>
                    <th class="w-20 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">No</th>
                    <th class="w-24 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Nama Group</th>
                    <th class="w-36 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($groups as $group)
                    <tr class="transition hover:bg-gray-50" data-search="{{ strtolower($group->id . ' ' . $group->name) }}">
                        <td class="px-4 py-3 text-center"><input form="bulkDeleteForm" type="checkbox" name="ids[]" class="bulk-checkbox rounded border-gray-300 text-blue-600" value="{{ $group->id }}" data-label="{{ $group->name }}" data-username="{{ $group->name }}"></td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $group->id }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $group->name }}</td>
                        <td class="px-4 py-3 text-center">@include('admin.groups.groups.partials.row-action')</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500"><i class="fa-solid fa-inbox mb-3 block text-3xl text-gray-300"></i>Data Group tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
