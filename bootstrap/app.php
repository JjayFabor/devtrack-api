<?php

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\UniqueConstraintViolationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        $middleware->trustProxies(
            at: ['*'],
            headers: Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO | Request::HEADER_X_FORWARDED_AWS_ELB
        );

        $middleware->prepend([
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        $middleware->alias([
            'api.key.throttle' => \App\Http\Middleware\ApikeyThrottle::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $e, $request) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        });

        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            return new JsonResponse([
                'success' => false,
                'errors' => [
                    'message' => 'Resource not found.',
                ]
            ], 404);
        });

        $exceptions->render(function (UniqueConstraintViolationException $e, $request) {
            $uniqueFields = ['name', 'email', 'name', 'key'];
            $field = null;
            $value = null;

            foreach ($uniqueFields as $uf) {
                if ($request->has($uf)) {
                    $field = $uf;
                    $value = $request->input($uf);
                    break;
                }
            }

            $message = ($field && $value) ? "The $field '$value' already exists." : 'Resource already exists.';
            return new JsonResponse([
                'success' => false,
                'errors' => [
                    'message' => $message,
                ]
            ], 409);

        });

    })
    ->create();
