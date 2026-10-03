<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$basePath = dirname(__DIR__);
$publicPath = $basePath.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'frontend'.DIRECTORY_SEPARATOR.'public';

$app = Application::configure(basePath: $basePath)
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            '2fa' => \App\Http\Middleware\EnsureTwoFactorVerified::class,
            'jwt' => \App\Http\Middleware\JwtAuthenticate::class,
            'permission' => \App\Http\Middleware\EnsurePermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*')) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    $status = 401;
                }

                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                    $status = $e->getStatusCode();
                }

                return response()->json([
                    'message' => $status >= 500 ? 'Error interno del servidor.' : ($e->getMessage() ?: 'Error'),
                ], $status >= 100 && $status < 600 ? $status : 500);
            }
        });
    })
    ->create();

$app->usePublicPath($publicPath);

return $app;
