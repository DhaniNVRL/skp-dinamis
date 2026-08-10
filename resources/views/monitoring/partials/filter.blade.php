<section class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
    <form method="GET" action="{{ route($dashboardRoute) }}" class="grid gap-4 {{ $isSurveyor ? 'lg:grid-cols-2' : 'lg:grid-cols-4' }}">
        @if (!empty($filters['status']))
            <input type="hidden" name="status" value="{{ $filters['status'] }}">
        @endif
        <div>
            <label for="monitoringUsername" class="mb-1 block text-sm font-medium text-gray-700">Username</label>
            <input id="monitoringUsername" name="username" type="search" value="{{ $filters['username'] ?? '' }}"
                   placeholder="Cari username..."
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        @unless ($isSurveyor)

        <div>
            <label for="monitoringGroup" class="mb-1 block text-sm font-medium text-gray-700">Group</label>
            <select id="monitoringGroup" name="group_id"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Semua Group</option>
                @foreach ($groups as $group)
                    <option value="{{ $group->id }}" @selected((string) ($filters['group_id'] ?? '') === (string) $group->id)>
                        {{ $group->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="monitoringUnit" class="mb-1 block text-sm font-medium text-gray-700">Unit</label>
            <select id="monitoringUnit" name="unit_id"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Semua Unit</option>
                @foreach ($units as $unit)
                    <option value="{{ $unit->id }}" data-group-id="{{ $unit->group_id }}"
                            @selected((string) ($filters['unit_id'] ?? '') === (string) $unit->id)>
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @endunless


        <div class="flex items-end gap-2">
            <a href="{{ route($dashboardRoute) }}"
               class="inline-flex flex-1 justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Reset
            </a>
            <button type="submit"
                    class="inline-flex flex-1 justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                Terapkan
            </button>
        </div>
    </form>
</section>
