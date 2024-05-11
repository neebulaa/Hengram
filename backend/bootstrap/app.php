<?php

use App\Http\Middleware\EnsureUserIsCreator;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1'
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'iscreator' => EnsureUserIsCreator::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/v1/posts/*')) {
                return response()->json([
                    'message' => 'Post not found.'
                ], 404);
            }
            
            if ($request->is('api/v1/users/*')) {
                return response()->json([
                    'message' => 'User not found.'
                ], 404);
            }
        });
    })->create();
