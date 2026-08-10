<div
    id="unitPagination"
    class="flex flex-col gap-3 rounded-lg
        border border-gray-200 bg-white px-4 py-3
        sm:flex-row sm:items-center sm:justify-between"
>
    <p
        id="unitPaginationInfo"
        class="text-sm text-gray-500"
    >
        Menampilkan data Unit
    </p>

    <div class="flex items-center gap-2">
        <button
            id="unitPreviousPage"
            type="button"
            class="rounded-lg border border-blue-500 bg-white
                px-3 py-2 text-sm text-blue-700
                transition hover:bg-gray-500/5
                disabled:cursor-not-allowed disabled:opacity-40"
        >
            Sebelumnya
        </button>

        <div
            id="unitPageNumbers"
            class="flex items-center gap-1"
        ></div>

        <button
            id="unitNextPage"
            type="button"
            class="rounded-lg border border-blue-500 bg-white
                px-3 py-2 text-sm text-blue-700
                transition hover:bg-gray-500/5
                disabled:cursor-not-allowed disabled:opacity-40"
        >
            Berikutnya
        </button>
    </div>
</div>