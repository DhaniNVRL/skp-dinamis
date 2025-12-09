<header class="bg-blue-700 text-white px-6 py-4 flex justify-between items-center">
    <div class="text-xl font-bold">Dashboard</div>

    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
            <span>{{ Auth::user()->name ?? 'User' }}</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div
            x-show="open"
            @click.away="open = false"
            x-transition
            class="absolute right-0 mt-2 w-40 bg-white text-gray-800 rounded shadow-lg z-50"
            style="display: none;">
            <a href="" class="block px-4 py-2 hover:bg-gray-100">Edit Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Logout</button>
            </form>
        </div>
    </div>
</header>
