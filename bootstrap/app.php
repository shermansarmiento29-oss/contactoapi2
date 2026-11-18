<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->respond(function ($response, Throwable $e, Request $request) {

            // Interceptar error 401 (Token ausente o inválido)
            if ($e instanceof AuthenticationException && $request->is('api/*')) {
                return response()->json([
                    'error' => [
                        'status' => 401,
                        'code' => 'UNAUTHORIZED',
                        'message' => 'No autenticado. Token inválido o ausente.',
                    ],
                ], 401);
            }

            // Interceptar error 403 (El recurso pertenece a otro usuario)
            if ($e instanceof HttpExceptionInterface && $e->getStatusCode() === 403 && $request->is('api/*')) {
                return response()->json([
                    'error' => [
                        'status' => 403,
                        'code' => 'FORBIDDEN',
                        'message' => 'usted no tiene los permisos necesarios para ver este recurso',
                    ],
                ], 403);
            }

            // Interceptar error 404 (Recurso no encontrado)
            if ($e instanceof NotFoundHttpException && $request->is('api/*')) {
                return response()->json([
                    'error' => [
                        'status' => 404,
                        'code' => 'NOT_FOUND',
                        'message' => 'recurso no encontrado',
                    ],
                ], 404);
            }

            return $response;
        });

    })->create();
