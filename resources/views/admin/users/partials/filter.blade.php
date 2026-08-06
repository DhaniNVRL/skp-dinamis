<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
    <form method="GET" action="{{ route('admin.datauser') }}">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-12 xl:items-end">
            <div class="xl:col-span-5">
                <label for="searchInput" class="mb-1 block text-sm font-medium text-gray-700">Cari User</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input id="searchInput" type="search" name="search" value="{{ request('search') }}" placeholder="Cari username atau nama lengkap..." class="h-10 w-full rounded-lg border border-gray-300 py-2 pl-10 pr-4 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
            </div>

            <div class="xl:col-span-2">
                <label for="activityFilter" class="mb-1 block text-sm font-medium text-gray-700">Activity</label>
                <select name="activity" id="activityFilter" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Activity</option>
                    @foreach ($activities as $activity)
                        <option value="{{ $activity->id }}" @selected(request('activity') == $activity->id)>{{ $activity->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="xl:col-span-2">
                <label for="roleFilter" class="mb-1 block text-sm font-medium text-gray-700">Role</label>
                <select name="role" id="roleFilter" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected(request('role') == $role->id)>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2 xl:col-span-3">
                <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700"><i class="fa-solid fa-magnifying-glass"></i>Cari</button>
                <a href="{{ route('admin.datauser') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fa-solid fa-rotate-left"></i>Reset</a>
            </div>
        </div>
    </form>
</div>
