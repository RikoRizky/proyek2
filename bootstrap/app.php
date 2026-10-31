<?php

// Sembunyikan notifikasi deprecation PHP 8.5+ agar tidak bocor ke output/tampilan
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

use App\Http\Middleware\EnsureUserRole;
use App\Support\AccreditationUpload;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => EnsureUserRole::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\CheckSubscriptionStatus::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'midtrans/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (PostTooLargeException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Total ukuran unggahan melebihi batas server.',
                ], 413);
            }

            $postLimit = AccreditationUpload::iniSizeLabel(ini_get('post_max_size'));
            $maxMb = AccreditationUpload::maxUploadMb();

            return redirect()
                ->back()
                ->withErrors([
                    'files' => 'Total ukuran unggahan melebihi batas server ('.$postLimit.'). '
                        .'Unggah lebih sedikit berkas sekaligus atau perkecil ukuran berkas (maks. '.$maxMb.' MB per berkas). '
                        .'Jika batas server terlalu kecil, jalankan ulang dengan `composer serve` atau sesuaikan post_max_size di php.ini.',
                ]);
        });
    })->create();
