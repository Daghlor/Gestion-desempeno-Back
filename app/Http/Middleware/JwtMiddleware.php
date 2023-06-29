<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        try {
            $headers = apache_request_headers();
            $request->headers->set('Authorization', $headers['Authorization']);

            $user = FacadesJWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(array(
                    'msg' => 'Usuario no logueado',
                    'code' => 'login_failed',
                    'redirect' => '/login',
                ), 200);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(array(
                    'msg' => 'Sesion Expiro',
                    'code' => 'login_failed',
                    'redirect' => '/login',
                ), 200);
            } else {
                return response()->json(array(
                    'msg' => 'No existe Autorización',
                    'code' => 'login_failed',
                    'redirect' => '/login',
                ), 200);
            }
        }
        return $next($request);
    }
}
