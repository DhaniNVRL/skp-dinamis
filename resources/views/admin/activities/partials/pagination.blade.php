<div class="flex flex-col gap-3 rounded-lg border border-gray-200 bg-white px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
    <p class="text-sm text-gray-500">Total {{ $activities->count() }} Activity</p>

    @if ($activities instanceof \Illuminate\Pagination\AbstractPaginator)
        <div>{{ $activities->links() }}</div>
    @endif
</div>
