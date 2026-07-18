<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) return;
            $code = 500;
            if ($e instanceof HttpException) $code = $e->getStatusCode();
            $file = dirname(__DIR__) . '/public/error.html';
            if (file_exists($file)) {
                $html = file_get_contents($file);
                $inject = '<script>window.__errorCode=' . $code . ';</script>';
                $html = str_replace('<script type="module">', $inject . '<script type="module">', $html);
                return response($html, $code)->header('Content-Type', 'text/html; charset=UTF-8');
            }
        });
    })->create();
