<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table id="userTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    {{-- CHECKBOX --}}
                    <th class="w-12 px-4 py-3 text-center">
                        <input
                            type="checkbox"
                            class="bulk-select-all rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        >
                    </th>

                    {{-- NO --}}
                    <th class="w-16 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                        No
                    </th>

                    {{-- ID USER --}}
                    <th class="w-24 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                        <div class="flex items-center gap-2">
                            <span>ID User</span>

                            <div class="flex flex-col items-center leading-none">

                                {{-- Panah atas: ID terbesar --}}
                                <a
                                    href="{{ request()->fullUrlWithQuery([
                                        'sort_by' => 'id',
                                        'sort_direction' => 'desc',
                                        'page' => 1,
                                    ]) }}"
                                    title="ID terbesar ke terkecil"
                                    aria-label="Urutkan ID terbesar ke terkecil"
                                    class="transition {{ request('sort_by', 'username') === 'id' && request('sort_direction', 'asc') === 'desc'
                                        ? 'text-blue-600'
                                        : 'text-gray-400 hover:text-blue-600' }}"
                                >
                                    <i class="fa-solid fa-caret-up"></i>
                                </a>

                                {{-- Panah bawah: ID terkecil --}}
                                <a
                                    href="{{ request()->fullUrlWithQuery([
                                        'sort_by' => 'id',
                                        'sort_direction' => 'asc',
                                        'page' => 1,
                                    ]) }}"
                                    title="ID terkecil ke terbesar"
                                    aria-label="Urutkan ID terkecil ke terbesar"
                                    class="transition {{ request('sort_by', 'username') === 'id' && request('sort_direction', 'asc') === 'asc'
                                        ? 'text-blue-600'
                                        : 'text-gray-400 hover:text-blue-600' }}"
                                >
                                    <i class="fa-solid fa-caret-down"></i>
                                </a>

                            </div>
                        </div>
                    </th>

                    {{-- USERNAME --}}
                    <th class="min-w-[160px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                        <div class="flex items-center gap-2">
                            <span>Username</span>

                            <div class="flex flex-col items-center leading-none">

                                {{-- Panah atas: Z ke A --}}
                                <a
                                    href="{{ request()->fullUrlWithQuery([
                                        'sort_by' => 'username',
                                        'sort_direction' => 'desc',
                                        'page' => 1,
                                    ]) }}"
                                    title="Username Z ke A"
                                    aria-label="Urutkan Username Z ke A"
                                    class="transition {{ request('sort_by', 'username') === 'username' && request('sort_direction', 'asc') === 'desc'
                                        ? 'text-blue-600'
                                        : 'text-gray-400 hover:text-blue-600' }}"
                                >
                                    <i class="fa-solid fa-caret-up"></i>
                                </a>

                                {{-- Panah bawah: A ke Z --}}
                                <a
                                    href="{{ request()->fullUrlWithQuery([
                                        'sort_by' => 'username',
                                        'sort_direction' => 'asc',
                                        'page' => 1,
                                    ]) }}"
                                    title="Username A ke Z"
                                    aria-label="Urutkan Username A ke Z"
                                    class="transition {{ request('sort_by', 'username') === 'username' && request('sort_direction', 'asc') === 'asc'
                                        ? 'text-blue-600'
                                        : 'text-gray-400 hover:text-blue-600' }}"
                                >
                                    <i class="fa-solid fa-caret-down"></i>
                                </a>

                            </div>
                        </div>
                    </th>

                    {{-- FULL NAME --}}
                    <th class="min-w-[200px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                        Full Name
                    </th>

                    {{-- ROLE --}}
                    <th class="min-w-[120px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                        Role
                    </th>

                    {{-- ACTIVITY --}}
                    <th class="min-w-[180px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                        Activity
                    </th>

                    {{-- GROUP --}}
                    <th class="min-w-[180px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                        Group
                    </th>

                    {{-- UNIT --}}
                    <th class="min-w-[180px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                        Unit
                    </th>

                    {{-- STATUS SURVEY --}}
                    <th class="min-w-[140px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                        Status Survey
                    </th>

                    {{-- KETERANGAN --}}
                    <th class="min-w-[180px] px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                        Keterangan
                    </th>

                    {{-- JAWABAN --}}
                    <th class="w-28 px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600">
                        Jawaban
                    </th>

                    {{-- AKSI --}}
                    <th class="w-36 px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600">
                        Aksi
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 bg-white">

                @forelse ($userProfiles as $profile)

                    @php
                        $profileUser = $profile->user;

                        if ($profileUser) {
                            $surveySession = $profileUser->surveySession;

                            $surveyStatus = $surveySession?->status ?? 'not_started';

                            $surveyStatusLabel = match ($surveyStatus) {
                                'completed' => 'Sudah Mengisi',
                                'in_progress' => 'Sedang Mengisi',
                                default => 'Belum Mengisi',
                            };

                            $surveyStatusClass = match ($surveyStatus) {
                                'completed' => 'bg-emerald-100 text-emerald-700',
                                'in_progress' => 'bg-amber-100 text-amber-700',
                                default => 'bg-slate-100 text-slate-600',
                            };
                        }
                    @endphp

                    @if ($profileUser)

                        <tr
                            class="transition hover:bg-gray-50"
                            data-search="{{ strtolower(
                                $profileUser->id . ' ' .
                                $profileUser->username . ' ' .
                                ($profile->fullname ?? '')
                            ) }}"
                            data-role="{{ $profileUser->role_id }}"
                            data-activity="{{ $profile->activity_id }}"
                        >

                            <td class="px-4 py-3 text-center">
                                <input
                                    type="checkbox"
                                    class="bulk-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    value="{{ $profileUser->id }}"
                                    data-username="{{ $profileUser->username }}"
                                >
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $profileUser->id }}
                            </td>

                            <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                {{ $profileUser->username }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $profile->fullname ?: '-' }}
                            </td>

                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700"
                                >
                                    {{ $profileUser->role?->name ?? '-' }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $profile->activity?->name ?: '-' }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $profile->group?->name ?: '-' }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $profile->unit?->name ?: '-' }}
                            </td>

                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $surveyStatusClass }}"
                                >
                                    {{ $surveyStatusLabel }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm">

                                @if ($surveySession?->reopened_at)

                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-semibold text-indigo-700"
                                        title="{{ $surveySession->reopened_at->format('d-m-Y H:i') }}"
                                    >
                                        <i class="fa-solid fa-lock-open"></i>
                                        Akun Dibuka Kembali
                                    </span>

                                @else

                                    <span class="text-gray-400">
                                        -
                                    </span>

                                @endif

                            </td>

                            <td class="px-4 py-3 text-center">

                                <a
                                    href="{{ route('admin.datauser.answers', $profileUser->id) }}"
                                    class="inline-flex min-w-10 items-center justify-center rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-200"
                                    title="Lihat jawaban user"
                                >
                                    {{ $profileUser->answers_count ?? 0 }}
                                </a>

                            </td>

                            <td class="px-4 py-3 text-center">

                                @include(
                                    'admin.users.partials.row-action',
                                    [
                                        'profile' => $profile,
                                        'profileUser' => $profileUser,
                                    ]
                                )

                            </td>

                        </tr>

                    @endif

                @empty

                    <tr>
                        <td
                            colspan="13"
                            class="px-4 py-12 text-center text-sm text-gray-500"
                        >
                            <i class="fa-regular fa-folder-open mb-2 block text-2xl text-gray-400"></i>

                            Belum ada data User.
                        </td>
                    </tr>

                @endforelse

            </tbody>
        </table>
    </div>
</div>
