<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terjadi Kesalahan</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; background: #f3f4f6; color: #1f2937; font-family: Arial, sans-serif; }
        .card { width: 100%; max-width: 560px; padding: 32px; border: 1px solid #e5e7eb; border-radius: 16px; background: #fff; box-shadow: 0 16px 40px rgba(0,0,0,.08); text-align: center; }
        .code { color: #dc2626; font-size: 54px; font-weight: 800; }
        h1 { margin: 8px 0 0; font-size: 24px; }
        p { margin: 12px 0 24px; color: #6b7280; line-height: 1.6; }
        a { display: inline-block; padding: 11px 18px; border-radius: 10px; background: #2563eb; color: #fff; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <main class="card" role="alert">
        <div class="code">500</div>
        <h1>Terjadi kesalahan pada aplikasi</h1>
        <p>Permintaan belum dapat diproses. Silakan coba kembali. Detail kesalahan telah dicatat untuk administrator.</p>
        <a href="{{ url('/') }}">Kembali ke halaman utama</a>
    </main>
</body>
</html>
