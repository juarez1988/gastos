<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;

class ApiCheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $alg = ["typ" => "JWT", "alg" => "HS256"];
        $jwt = $request->header('token');
        $key = 'JWT_SECRET';
        try{
            JWT::decode($jwt,$key,$alg);
        }
        catch (\Exception $e){
                         return response () -> json ([
                            'status' => 'error',
                            'code' => 404,
                            'message' => 'El token no es correcto...'
                        ]);
        }
        return $next($request);
    }
}
