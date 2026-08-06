<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard')</title>

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- Alpine --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Sortable --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    {{-- Font Awesome --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


    <style>
        [x-cloak]{
            display:none !important;
        }
    </style>

    @stack('styles')

</head>

<body class="h-screen overflow-hidden bg-gray-100">

    {{-- Header --}}
    @include('admin.layouts.header')

    <div class="flex flex-1 overflow-hidden">

        {{-- Sidebar --}}
        @include('admin.layouts.sidebar')

        {{-- Content --}}
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

    {{-- Global JS --}}
    <script src="{{ asset('js/global-modal.js') }}"></script>
    <script src="{{ asset('js/global-modal-tab.js') }}"></script>
    <script src="{{ asset('js/global-form.js') }}"></script>

    @stack('scripts')

</body>
</html>