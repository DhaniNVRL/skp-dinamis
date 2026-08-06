
<div
    id="editUserModal"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-6"
>
    <div class="w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-xl">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">

            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Edit Akun
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Ubah informasi akun yang dipilih.
                </p>
            </div>

            <button
                type="button"
                data-modal-close="editUserModal"
                class="text-gray-400 transition hover:text-red-600"
            >
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>

        </div>


        <form
            id="editUserForm"
            method="POST"
        >
            @csrf
            @method('PUT')


            {{-- ID --}}
            <input
                type="hidden"
                name="id"
                id="edit_user_id"
            >


            {{-- Body --}}
            <div class="space-y-5 p-6">

                {{-- USERNAME --}}
                <div>

                    <label
                        for="edit_username"
                        class="mb-2 block text-sm font-medium text-gray-700"
                    >
                        Username
                    </label>

                    <input
                        type="text"
                        name="username"
                        id="edit_username"
                        required
                        autocomplete="off"
                        placeholder="Username"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >

                </div>


                {{-- PASSWORD --}}
                <div>

                    <label
                        for="edit_password"
                        class="mb-2 block text-sm font-medium text-gray-700"
                    >
                        Password Baru
                    </label>

                    <input
                        type="password"
                        name="password"
                        id="edit_password"
                        autocomplete="new-password"
                        placeholder="Masukkan password baru"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >

                    <p class="mt-2 text-sm text-red-500">
                        Biarkan kolom ini kosong jika Anda tidak ingin mengubah kata sandi.
                    </p>

                </div>


                {{-- ROLE --}}
                <div>

                    <label
                        for="edit_role"
                        class="mb-2 block text-sm font-medium text-gray-700"
                    >
                        Role
                    </label>

                    <select
                        name="role_id"
                        id="edit_role"
                        required
                        class="role-select w-full rounded-lg border border-gray-300 bg-white px-3 py-2
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >

                        <option value="">
                            Pilih Role
                        </option>

                        @foreach($roles as $role)

                            <option value="{{ $role->id }}">
                                {{ $role->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- ACTIVITY --}}
                <div class="activity-column">

                    <label
                        for="edit_activity"
                        class="mb-2 block text-sm font-medium text-gray-700"
                    >
                        Activity
                    </label>

                    <select
                        name="activity_id"
                        id="edit_activity"
                        required
                        class="activity-select w-full rounded-lg border border-gray-300 bg-white px-3 py-2
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >

                        <option value="">
                            Pilih Activity
                        </option>

                        @foreach($activities as $activity)

                            <option value="{{ $activity->id }}">
                                {{ $activity->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>


            {{-- Footer --}}
            <div class="flex items-center justify-end gap-2 border-t border-gray-200 bg-gray-50 px-6 py-4">

                <button
                    type="button"
                    data-modal-close="editUserModal"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700
                           transition hover:bg-gray-100"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-2 text-white
                           transition hover:bg-blue-700"
                >
                    <i class="fa-solid fa-floppy-disk mr-1"></i>
                    Simpan
                </button>

            </div>

        </form>

    </div>
</div>