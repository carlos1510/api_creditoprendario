<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try{
            JWTAuth::parseToken()->authenticate();
        }catch(Exception $ex){
            if($ex instanceof TokenInvalidException){
                return response()->json(['status' => 401,'message' => 'Invalid token'], 401);
            }

            if($ex instanceof TokenExpiredException){
                return response()->json(['status'=> 401,'message' => 'expired token'], 401);
            }

            return response()->json(['status'=> 401,'message' => 'token not found'], 401);
        }
        return $next($request);
    }
}
