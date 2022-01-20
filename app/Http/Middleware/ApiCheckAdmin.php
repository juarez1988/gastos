<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;

class ApiCheckAdmin
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
            $user = JWT::decode($jwt,$key,$alg);
        }
        catch (\Exception $e){
                         return response () -> json ([
                            'status' => 'error',
                            'code' => 404,
                            'message' => 'El token no es correcto...'
                        ]);
        }
        if($user->role == 'admin' && $user->active == 'Y'){
            return $next($request);
        }else{
            return response () -> json ([
                'status' => 'error',
                'code' => 404,
                'message' => 'No tiene permisos para esta seccion...'
            ]);
        }
    }
}
