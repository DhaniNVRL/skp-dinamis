<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Hello World</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=20260820">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=20260820">
    @vite('resources/js/app.js')
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <h1 class="text-4xl font-bold text-blue-600">Hello World</h1>
</body>
</html>
