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
</head>
<body class="flex flex-col min-h-screen bg-gray-100">

    {{-- Header --}}
    @include('admin.layouts.header')

    <div class="flex flex-1 overflow-hidden">
        {{-- Sidebar --}}
         @include('admin.layouts.sidebar')

        {{-- Main Content --}}
        <main class="flex-1 p-6 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    {{-- Footer --}}
    @include('admin.layouts.footer')

</body>
</html>
