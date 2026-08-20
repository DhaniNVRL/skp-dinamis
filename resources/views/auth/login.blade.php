<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=20260820">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=20260820">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    @include('layouts.partials.global-alerts')
    <div id="pageContent" class="bg-white rounded shadow-md w-full max-w-4xl flex flex-col md:flex-row overflow-hidden">

        <!-- Gambar (Hanya tampil di laptop/PC) -->
        <div class="hidden md:block md:w-1/2">
            <img src="{{ asset('images/login.png') }}"
                alt="Login Image"
                class="w-full h-full object-cover">
        </div>

        <!-- Form Login -->
        <div class="w-full md:w-1/2 p-8">
            <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

            @if (session('finish'))
                <div
                    id="successAlert"
                    data-alert
                    class="mb-6 flex items-start justify-between
                        rounded-lg border border-green-200
                        bg-green-50 px-4 py-3 text-green-700"
                >
                    <div class="flex items-start gap-3">
                        <div
                            class="mt-0.5 flex h-6 w-6
                                shrink-0 items-center
                                justify-center rounded-full
                                bg-green-100"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                class="h-4 w-4"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>

                        <div>
                            <div class="mt-1 text-sm">
                                {{ session('status') }}
                            </div>
                        </div>
                    </div>

                    <button
                        type="button"
                        onclick="document.getElementById('successAlert').remove()"
                        class="ml-4 text-green-500
                            transition hover:text-green-700"
                        aria-label="Tutup notifikasi"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            class="h-5 w-5"
                        >
                            <path
                                d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"
                            />
                        </svg>
                    </button>
                </div>
            @endif

            @if (session('status'))
                <div data-alert class="text-center mb-4 text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div data-alert class="text-center mb-4 text-red-600">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700">Username</label>
                    <input type="username" name="username" value="{{ old('username') }}" required autofocus class="w-full px-4 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300">
                </div>

                <div class="mb-4 flex items-center">
                    <input type="checkbox" name="remember" class="mr-2">
                    <label class="text-gray-700">Ingat saya</label>
                </div>

                <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">
                    Login
                </button>

                <p class="mt-4 text-center text-sm text-gray-600">
                    Pembuatan akun dikelola oleh administrator.
                </p>
            </form>
        </div>
    </div>
    <script src="{{ asset('js/global-alerts.js') }}?v=20260806-4"></script>
</body>
</html>
