<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try
        {
            $user = JWTAuth::parseToken()->authenticate();
        }
        catch(\Tymon\JWTAuth\Exceptions\TokenInvalidException $exception)
        {
            return response()->json(['status' => 'Token is Invalid']);
        }
        catch(\Tymon\JWTAuth\Exceptions\TokenExpiredException $exception)
        {
            return response()->json(['status' => 'Token is Expired']);
        }
        catch(Exception $exception)
        {
            return response()->json(['status' => 'Authorization Token not found']);
        }

        return $next($request);
    }
}
