<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white rounded shadow-md w-full max-w-4xl flex flex-col md:flex-row overflow-hidden">

        <!-- Gambar (Hanya tampil di laptop/PC) -->
        <div class="hidden md:block md:w-1/2">
            <img src="https://source.unsplash.com/600x800/?login,technology" alt="Login Image" class="w-full h-full object-cover">
        </div>

        <!-- Form Login -->
        <div class="w-full md:w-1/2 p-8">
            <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

            @if (session('status'))
                <div class="text-center mb-4 text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="text-center mb-4 text-red-600">
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
                    Belum punya akun?
                    <a href="/register" class="text-blue-600 hover:underline font-semibold">Buat Akun</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
