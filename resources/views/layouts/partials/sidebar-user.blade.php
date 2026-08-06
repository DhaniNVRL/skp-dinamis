<aside
    id="userSidebar"
    class="fixed bottom-12 left-0 top-16 z-40 w-64 overflow-y-auto border-r border-gray-800 bg-gray-900 text-white"
>
    <div class="flex min-h-full flex-col">
        {{-- USER INFORMATION --}}
        <div class="border-b border-gray-800 px-5 py-5">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-600 font-semibold text-white">
                    {{ strtoupper(
                        substr(
                            auth()->user()->username
                                ?? 'U',
                            0,
                            1
                        )
                    ) }}
                </div>

                <div class="min-w-0">
                    <div class="truncate text-sm font-semibold text-white">
                        {{ auth()->user()->username
                            ?? 'Responden' }}
                    </div>

                    <div class="mt-0.5 text-xs text-gray-400">
                        Responden
                    </div>
                </div>
            </div>
        </div>

        {{-- NAVIGATION --}}
        <nav class="flex-1 space-y-6 px-3 py-5">
            {{-- DASHBOARD --}}
            <div>
                <div class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">
                    Dashboard
                </div>

                <div class="space-y-1">
                    <a
                        href="{{ route('user.dashboard') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition
                            {{ request()->routeIs('user.dashboard')
                                ? 'bg-blue-600 text-white'
                                : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    >
                        <i class="fa-solid fa-house w-5 text-center"></i>
                        <span>Beranda</span>
                    </a>
                </div>
            </div>

            {{-- SURVEY --}}
            <div>
                <div class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">
                    Survei
                </div>

                <div class="space-y-1">
                    <a
                        href="{{ route('survey.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition
                            {{ request()->routeIs('survey.*')
                                ? 'bg-blue-600 text-white'
                                : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    >
                        <i class="fa-solid fa-clipboard-list w-5 text-center"></i>
                        <span>Isi Survei</span>
                    </a>
                </div>
            </div>

            {{-- ACCOUNT --}}
            <div>
                <div class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">
                    Akun
                </div>

                <div class="space-y-1">
                    <a
                        href="{{ route('profile.show') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition
                            {{ request()->routeIs(
                                'profile.show',
                                'profile.edit'
                            )
                                ? 'bg-blue-600 text-white'
                                : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    >
                        <i class="fa-solid fa-user w-5 text-center"></i>
                        <span>Profil</span>
                    </a>
                </div>
            </div>
        </nav>

        {{-- LOGOUT --}}
        <div class="border-t border-gray-800 p-3">
            <form
                method="POST"
                action="{{ route('logout') }}"
            >
                @csrf

                <button
                    type="submit"
                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-red-400 transition hover:bg-red-500/10 hover:text-red-300"
                >
                    <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>
</aside>