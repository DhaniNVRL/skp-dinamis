<form
    id="subUnitFilterForm"
    action="{{ route('admin.subunit', [
        'id' => $units->id,
    ]) }}"
    method="GET"
    class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm"
>
    <input
        type="hidden"
        name="tab"
        value="subunit"
    >

    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div class="relative w-full md:max-w-md">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>

            <input
                id="searchSubUnit"
                type="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama Sub Unit..."
                autocomplete="off"
                class="w-full rounded-lg border border-gray-300 py-2 pl-10 pr-4 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
            >
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button
                type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
            >
                <i class="fa-solid fa-magnifying-glass"></i>
                Cari
            </button>

            @if (request()->filled('search'))
                <a
                    href="{{ route('admin.subunit', [
                        'id' => $units->id,
                        'tab' => 'subunit',
                    ]) }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
                >
                    <i class="fa-solid fa-rotate-left"></i>
                    Reset
                </a>
            @endif
        </div>
    </div>
</form>