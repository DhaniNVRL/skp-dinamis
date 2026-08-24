<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-sucofindo.png') }}?v=20260820-sucofindo-2">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    @include('layouts.partials.global-alerts')
    <div id="pageContent" class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Buat Akun Admin</h2>

        @if ($errors->any())
            <div data-alert class="mb-4 text-red-600">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" required
                       class="w-full px-4 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full px-4 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300">
            </div>

            <p class="mt-4 text-center text-sm text-gray-600">
                Sudah punya akun?
                <a href="/" class="text-blue-600 hover:underline font-semibold">Login</a>
            </p>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">
                Daftar
            </button>
        </form>
    </div>
    <script src="{{ asset('js/global-alerts.js') }}?v=20260806-4"></script>
</body>
</html>
