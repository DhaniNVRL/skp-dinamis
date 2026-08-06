<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
    <form id="monitoringFilterForm" method="GET" action="{{ url()->current() }}">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-12 xl:items-end">
            <div class="xl:col-span-3">
                <label for="usernameFilter" class="mb-1 block text-sm font-medium text-gray-700">Username / Nama</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input id="usernameFilter" name="username" value="{{ $filters['username'] ?? '' }}" type="search" placeholder="Cari responden..." class="h-10 w-full rounded-lg border border-gray-300 py-2 pl-10 pr-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
            </div>

            <div class="xl:col-span-2">
                <label for="activityFilter" class="mb-1 block text-sm font-medium text-gray-700">Activity</label>
                <select id="activityFilter" name="activity_id" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Activity</option>
                    @foreach ($activities as $activity)
                        <option value="{{ $activity->id }}" @selected(($filters['activity_id'] ?? '') == $activity->id)>{{ $activity->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="xl:col-span-2">
                <label for="groupFilter" class="mb-1 block text-sm font-medium text-gray-700">Group</label>
                <select id="groupFilter" name="group_id" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Group</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}" data-activity-id="{{ $group->activity_id }}" @selected(($filters['group_id'] ?? '') == $group->id)>{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="xl:col-span-2">
                <label for="unitFilter" class="mb-1 block text-sm font-medium text-gray-700">Unit</label>
                <select id="unitFilter" name="unit_id" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Unit</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}" data-group-id="{{ $unit->group_id }}" @selected(($filters['unit_id'] ?? '') == $unit->id)>{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="xl:col-span-3">
                <label for="statusFilter" class="mb-1 block text-sm font-medium text-gray-700">Status Survey</label>
                <select id="statusFilter" name="status" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="completed" @selected(($filters['status'] ?? '') === 'completed')>Sudah Mengisi</option>
                    <option value="in_progress" @selected(($filters['status'] ?? '') === 'in_progress')>Sedang Mengisi</option>
                    <option value="not_started" @selected(($filters['status'] ?? '') === 'not_started')>Belum Mengisi</option>
                </select>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap justify-end gap-2 border-t border-gray-100 pt-4">
            <a href="{{ url()->current() }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fa-solid fa-rotate-left"></i>Reset</a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white hover:bg-blue-700"><i class="fa-solid fa-filter"></i>Terapkan Filter</button>
        </div>
    </form>
</div>
