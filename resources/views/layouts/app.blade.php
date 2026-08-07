<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>@yield('title', 'Dashboard')</title>

    {{-- Fallback utility CSS; build Vite lama belum memproses Tailwind. --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >

    {{-- LIBRARY --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>

    <script
        defer
        src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"
    ></script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
    ])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @stack('styles')
</head>

<body class="h-screen overflow-hidden bg-gray-100">
    {{-- HEADER --}}
    @include('layouts.partials.header')

    <div class="flex h-[calc(100vh-4rem)]">
        {{-- SIDEBAR --}}
        @auth
            @if (auth()->user()->hasRole('admin'))
                @include(
                    'layouts.partials.sidebar-admin'
                )

            @elseif (auth()->user()->hasRole('pm'))
                @include(
                    'layouts.partials.sidebar-pm'
                )

            @elseif (auth()->user()->hasRole('surveyor'))
                @include(
                    'layouts.partials.sidebar-surveyor'
                )

            @else
                @include(
                    'layouts.partials.sidebar-user'
                )
            @endif
        @endauth

        {{-- MAIN CONTENT --}}
        <main
            class="fixed bottom-12 left-64 right-0 top-16 overflow-y-auto bg-gray-100 px-6 py-6"
        >
            @include('layouts.partials.global-alerts')

            <div id="pageContent">
                @yield('content')
            </div>
        </main>
    </div>

    {{-- FOOTER --}}
    @include('layouts.partials.footer')

    @stack('modals')
    @stack('templates')
    <script src="{{ asset('js/global-alerts.js') }}?v=20260806-4"></script>
    <script src="{{ asset('js/active-tab-persistence.js') }}?v=20260806-1"></script>
    @stack('scripts')
</body>
</html>
