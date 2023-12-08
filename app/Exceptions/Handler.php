<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof NotFoundHttpException) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Modelo no encontrado',
                    'error' => $e->getMessage(),
                ], 404);
            } else {
                return response()->json([
                    'message' => $e->getMessage()
                ], 402);
            }
        }
        if ($e instanceof AuthorizationException) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => 'No tienes los permisos necesarios para realizar esta acción.',
                ], 403);
            }
        }
        return parent::render($request, $e);
    }
}
