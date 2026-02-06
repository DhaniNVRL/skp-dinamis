<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <style>
        const modal = document.getElementById('globalModal');
    </style>


</head>
<body class="h-screen overflow-hidden bg-gray-100">


    {{-- Header --}}
    @include('admin.layouts.header')

    <div class="flex flex-1 overflow-hidden">
        {{-- Sidebar --}}
         @include('admin.layouts.sidebar')

        {{-- Main Content --}}
        <main
        class="fixed
                top-16 bottom-12
                left-64 right-0
                px-6 py-6
                overflow-y-auto
                bg-gray-100">

            @yield('content')

        </main>
    </div>

    {{-- Footer --}}
    @include('admin.layouts.footer')
    <script src="{{ asset('js/global-modal.js') }}"></script>
    <script src="{{ asset('js/global-modal-tab.js') }}"></script>
</body>
</html>
