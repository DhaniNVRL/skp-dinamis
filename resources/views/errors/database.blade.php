<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gangguan Database</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; background: #f3f4f6; color: #1f2937; font-family: Arial, sans-serif; }
        .card { width: 100%; max-width: 560px; padding: 32px; border: 1px solid #fecaca; border-radius: 16px; background: #fff; box-shadow: 0 16px 40px rgba(0,0,0,.08); text-align: center; }
        .icon { display: grid; place-items: center; width: 64px; height: 64px; margin: 0 auto 20px; border-radius: 999px; background: #fee2e2; color: #dc2626; font-size: 30px; font-weight: bold; }
        h1 { margin: 0; font-size: 24px; }
        p { margin: 12px 0 24px; color: #6b7280; line-height: 1.6; }
        a { display: inline-block; padding: 11px 18px; border-radius: 10px; background: #2563eb; color: #fff; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <main class="card" role="alert">
        <div class="icon" aria-hidden="true">!</div>
        <h1>Data belum dapat dimuat</h1>
        <p>{{ $message ?? 'Proses database gagal. Silakan coba kembali atau hubungi administrator.' }}</p>
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}">Kembali</a>
    </main>
</body>
</html>
