<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;

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
                        'message' => 'Token de autenticación ausente o inválido.'
                    ]
                ], 401);
            }

            // Interceptar error 404 (Recurso no encontrado)
            if ($e instanceof NotFoundHttpException && $request->is('api/*')) {
                return response()->json([
                    'error' => [
                        'status' => 404,
                        'code' => 'RESOURCE_NOT_FOUND',
                        'message' => 'El recurso solicitado no existe o fue eliminado.'
                    ]
                ], 404);
            }

            return $response;
        });
        
    })->create();