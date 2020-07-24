<?php

namespace App\Http\Middleware;
use App\User;
use Exception;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class JwtController
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $token = $request->bearerToken();
        $key = "kode_rahasia";

        if ( !$token ){
            return response()->json([
                'status' => 'error',
                'messages' => 'Token not provided'
            ], 401);
        }

        try {
            $credentials = JWT::decode($token, $key, array('HS256'));
        } catch (ExpiredException $e){  
            return response()->json([
                'status' => 'error',
                'messages' => 'Provided token is expired'
            ], 400);
        } catch (Exception $e){
            return response()->json([
                'status' => 'error',
                'messages' => 'Error while decoding token'
            ], 400);
        }
        return $next($request);
    }


}
