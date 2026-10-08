<div id="bulkEditUserModal" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white shadow-xl">
        <form id="bulkEditUserForm" method="POST" action="{{ route('admin.datauser.bulk-update') }}">
            @csrf
            @method('PUT')

            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Edit User Terpilih</h3>
                    <p class="mt-1 text-sm text-gray-500"><span id="bulkEditUserCount">0</span> akun akan diperbarui.</p>
                </div>
                <button type="button" data-modal-close="bulkEditUserModal" class="text-gray-400 hover:text-red-600"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>

            <div id="bulkEditUserIds"></div>

            <div class="space-y-4 p-6">
                @if ($errors->has('bulk_edit'))
                    <p class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $errors->first('bulk_edit') }}</p>
                @endif

                <p class="rounded-lg border border-blue-200 bg-blue-50 p-3 text-sm text-blue-700">Centang hanya field yang ingin diterapkan ke seluruh akun terpilih. Field yang tidak dicentang tidak berubah.</p>

                <div class="grid gap-3 rounded-lg border border-gray-200 p-4 sm:grid-cols-[auto_minmax(0,1fr)] sm:items-center">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700"><input type="checkbox" name="apply_role" value="1" data-bulk-apply="bulkRoleId" class="rounded border-gray-300 text-amber-600"> Ubah Role</label>
                    <select id="bulkRoleId" name="role_id" disabled class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 disabled:bg-gray-100">
                        <option value="">Pilih Role</option>
                        @foreach ($roles as $role)<option value="{{ $role->id }}">{{ $role->name }}</option>@endforeach
                    </select>
                </div>

                <div class="grid gap-3 rounded-lg border border-gray-200 p-4 sm:grid-cols-[auto_minmax(0,1fr)] sm:items-center">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700"><input type="checkbox" name="apply_activity" value="1" data-bulk-apply="bulkActivityId" class="rounded border-gray-300 text-amber-600"> Ubah Activity</label>
                    <select id="bulkActivityId" name="activity_id" disabled class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 disabled:bg-gray-100">
                        <option value="">Kosongkan Activity</option>
                        @foreach ($activities as $activity)<option value="{{ $activity->id }}">{{ $activity->name }}</option>@endforeach
                    </select>
                </div>

                <div class="grid gap-3 rounded-lg border border-gray-200 p-4 sm:grid-cols-[auto_minmax(0,1fr)] sm:items-center">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700"><input type="checkbox" name="apply_group" value="1" data-bulk-apply="bulkGroupId" class="rounded border-gray-300 text-amber-600"> Ubah Group</label>
                    <select id="bulkGroupId" name="group_id" disabled class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 disabled:bg-gray-100">
                        <option value="">Kosongkan Group</option>
                        @foreach ($groups as $group)<option value="{{ $group->id }}" data-activity-id="{{ $group->activity_id }}">{{ $group->name }}</option>@endforeach
                    </select>
                </div>

                <div class="grid gap-3 rounded-lg border border-gray-200 p-4 sm:grid-cols-[auto_minmax(0,1fr)] sm:items-center">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700"><input type="checkbox" name="apply_unit" value="1" data-bulk-apply="bulkUnitId" class="rounded border-gray-300 text-amber-600"> Ubah Unit</label>
                    <select id="bulkUnitId" name="unit_id" disabled class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 disabled:bg-gray-100">
                        <option value="">Kosongkan Unit</option>
                        @foreach ($units as $unit)<option value="{{ $unit->id }}" data-group-id="{{ $unit->group_id }}">{{ $unit->name }}</option>@endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4">
                <button type="button" data-modal-close="bulkEditUserModal" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Batal</button>
                <button type="submit" class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600"><i class="fa-solid fa-floppy-disk mr-2"></i>Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
