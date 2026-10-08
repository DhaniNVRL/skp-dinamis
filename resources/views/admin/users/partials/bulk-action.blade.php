<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-2 text-sm text-gray-600">
        <input id="selectAllUser" type="checkbox" class="bulk-select-all rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        <label for="selectAllUser">Pilih Semua</label>
    </div>

    <div class="flex flex-wrap gap-2">
        <button id="btnBulkEditUser" type="button" class="rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-700 transition hover:bg-amber-100 disabled:cursor-not-allowed disabled:opacity-50" disabled>
            <i class="fa-solid fa-pen-to-square mr-1"></i>Edit yang Dipilih
        </button>
        <button id="btnBulkResetAccount" type="button" class="rounded-lg border border-red-300 bg-red-50 px-3 py-2 text-sm font-medium text-red-700 transition hover:bg-red-100 disabled:cursor-not-allowed disabled:opacity-50" disabled>
            <i class="fa-solid fa-rotate-left mr-1"></i>Reset Account
        </button>
        <button id="btnBulkUnlockSubmit" type="button" class="rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700 transition hover:bg-emerald-100 disabled:cursor-not-allowed disabled:opacity-50" disabled>
            <i class="fa-solid fa-lock-open mr-1"></i>Unlock Submit
        </button>
    </div>
</div>
