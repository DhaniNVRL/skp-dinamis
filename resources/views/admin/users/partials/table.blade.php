<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table id="userTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="w-12 px-4 py-3 text-center"><input type="checkbox" class="bulk-select-all rounded border-gray-300 text-blue-600 focus:ring-blue-500"></th>
                    <th class="w-16 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">No</th>
                    <th class="w-24 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">ID User</th>
                    <th class="min-w-[160px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Username</th>
                    <th class="min-w-[200px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Full Name</th>
                    <th class="min-w-[120px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Role</th>
                    <th class="min-w-[180px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Activity</th>
                    <th class="min-w-[180px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Group</th>
                    <th class="min-w-[180px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Unit</th>
                    <th class="w-36 px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($userProfiles as $profile)
                    @php($profileUser = $profile->user)

                    @continue(! $profileUser)

                    <tr
                        class="transition hover:bg-gray-50"
                        data-search="{{ strtolower($profileUser->id . ' ' . $profileUser->username . ' ' . ($profile->fullname ?? '')) }}"
                        data-role="{{ $profileUser->role_id }}"
                        data-activity="{{ $profile->activity_id }}"
                    >
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" class="bulk-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $profileUser->id }}" data-username="{{ $profileUser->username }}">
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $profileUser->id }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $profileUser->username }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $profile->fullname ?: '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">{{ $profileUser->role?->name ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $profile->activity?->name ?: '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $profile->group?->name ?: '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $profile->unit?->name ?: '-' }}</td>
                        <td class="px-4 py-3 text-center">@include('admin.users.partials.row-action', ['profile' => $profile])</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-4 py-12 text-center text-sm text-gray-500">
                            <i class="fa-regular fa-folder-open mb-2 block text-2xl text-gray-400"></i>
                            Belum ada data User.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
