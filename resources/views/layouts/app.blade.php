<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>@yield('title', 'Dashboard')</title>

    {{-- CDN Tailwind dapat dihapus jika Vite sudah stabil --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >

    {{-- LIBRARY --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <script
        defer
        src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"
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
            @includeWhen(
                view()->exists('layouts.partials.alert'),
                'layouts.partials.alert'
            )

            @yield('content')
        </main>
    </div>

    {{-- FOOTER --}}
    @include('layouts.partials.footer')

    @stack('modals')
    @stack('templates')
    @stack('scripts')
</body>
</html>