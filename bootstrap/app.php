<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\PreserveActiveTab::class,
        ]);

        $middleware->alias([
            'check.profile' => \App\Http\Middleware\CheckUserProfile::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontFlash([
            'current_password',
            'password',
            'password_confirmation',
        ]);

        $exceptions->render(function (QueryException $exception, Request $request) {
            $sqlState = (string) ($exception->errorInfo[0] ?? $exception->getCode());
            $driverMessage = strtolower((string) $exception->getPrevious()?->getMessage());

            [$errorCode, $message, $status] = match (true) {
                str_contains($driverMessage, 'unique constraint')
                    || str_contains($driverMessage, 'duplicate entry')
                    || str_contains($driverMessage, 'duplicate key')
                    => [
                        'DB_DUPLICATE',
                        'Data yang sama sudah tersimpan. Gunakan nilai lain lalu coba kembali.',
                        409,
                    ],

                str_contains($driverMessage, 'foreign key constraint')
                    || str_contains($driverMessage, 'violates foreign key')
                    || str_contains($driverMessage, 'parent row')
                    => [
                        'DB_RELATION',
                        'Data tidak dapat diubah atau dihapus karena masih digunakan oleh data lain.',
                        409,
                    ],

                str_contains($driverMessage, 'not null constraint')
                    || str_contains($driverMessage, 'cannot be null')
                    || str_contains($driverMessage, 'not-null constraint')
                    => [
                        'DB_REQUIRED_VALUE',
                        'Ada data wajib yang belum terisi. Periksa kembali seluruh kolom input.',
                        422,
                    ],

                str_contains($driverMessage, 'database is locked')
                    || str_contains($driverMessage, 'deadlock')
                    || str_contains($driverMessage, 'lock wait timeout')
                    => [
                        'DB_BUSY',
                        'Database sedang sibuk atau terkunci. Tunggu beberapa saat lalu coba kembali.',
                        503,
                    ],

                str_contains($driverMessage, "field 'id' doesn't have a default value")
                    || str_contains($driverMessage, 'field `id` doesn\'t have a default value')
                    => [
                        'DB_SCHEMA',
                        'Kolom ID database belum dikonfigurasi otomatis. Jalankan pembaruan struktur database atau hubungi administrator.',
                        500,
                    ],

                str_contains($driverMessage, 'no such table')
                    || str_contains($driverMessage, 'no such column')
                    || str_contains($driverMessage, 'unknown column')
                    || str_contains($driverMessage, 'does not exist')
                    => [
                        'DB_SCHEMA',
                        'Struktur database belum sesuai dengan versi aplikasi. Hubungi administrator.',
                        500,
                    ],

                str_contains($driverMessage, "doesn't have a default value")
                    => [
                        'DB_REQUIRED_VALUE',
                        'Ada nilai wajib database yang belum dikirim. Periksa kembali seluruh kolom input.',
                        422,
                    ],

                str_contains($driverMessage, 'data too long')
                    || str_contains($driverMessage, 'value too long')
                    => [
                        'DB_VALUE_TOO_LONG',
                        'Nilai input melebihi panjang maksimum kolom database.',
                        422,
                    ],

                str_contains($driverMessage, 'incorrect integer value')
                    || str_contains($driverMessage, 'out of range value')
                    || str_contains($driverMessage, 'invalid input syntax')
                    => [
                        'DB_INVALID_VALUE',
                        'Jenis atau format nilai input tidak sesuai dengan kolom database.',
                        422,
                    ],

                str_contains($driverMessage, 'syntax error')
                    || str_contains($driverMessage, 'sql syntax')
                    => [
                        'DB_QUERY',
                        'Perintah penyimpanan aplikasi tidak sesuai dengan struktur database.',
                        500,
                    ],

                str_contains($driverMessage, 'connection refused')
                    || str_contains($driverMessage, 'could not connect')
                    || str_contains($driverMessage, 'server has gone away')
                    || str_contains($driverMessage, 'access denied')
                    => [
                        'DB_CONNECTION',
                        'Koneksi ke database gagal. Periksa layanan database atau hubungi administrator.',
                        503,
                    ],

                in_array($sqlState, ['19', '23000', '23503', '23505'], true)
                    => [
                        'DB_CONSTRAINT',
                        'Data ditolak karena duplikat atau masih mempunyai hubungan dengan data lain.',
                        409,
                    ],

                default => [
                    'DB_ERROR',
                    'Proses database gagal. Silakan coba kembali atau hubungi administrator.',
                    500,
                ],
            };

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'errors' => [],
                    'error_code' => $errorCode,
                    'safe_to_display' => true,
                ], $status);
            }

            if ($request->isMethodSafe()) {
                return response()->view('errors.database', [
                    'message' => $message,
                ], $status);
            }

            return redirect()
                ->back()
                ->withInput($request->except([
                    '_token',
                    'current_password',
                    'password',
                    'password_confirmation',
                    'file',
                ]))
                ->with('error', "Database menolak proses. Alasan: {$message} (Kode: {$errorCode})");
        });
    })
    ->create();
