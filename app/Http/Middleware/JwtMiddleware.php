<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
   
            if(!$user->active){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuario desactivado.',
                ], JsonResponse::HTTP_UNAUTHORIZED);
            }
            
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['message' => 'Token inválido'], JsonResponse::HTTP_UNAUTHORIZED);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['message' => 'Token ha expirado'], JsonResponse::HTTP_UNAUTHORIZED);
            } else {
                return response()->json(['message' => 'No se encontró el token de autorización'], JsonResponse::HTTP_UNAUTHORIZED);
            }
        }
        return $next($request);
    }
}
