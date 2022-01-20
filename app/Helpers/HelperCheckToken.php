<?php
namespace App\Helpers;

use Firebase\JWT\JWT;

class HelperCheckToken{

    private $key = 'JWT_SECRET';

    public function decodeToken($token){
        $alg = ["typ" => "JWT", "alg" => "HS256"];
        $jwt = $token;
        try{
            $decodeToken = JWT::decode($jwt, $this->key, $alg);
            return $decodeToken;
        }
        catch (\Exception $e){
            return false;
        }
    }

    public function encodeToken($value){
        $time = time();
        $payload = [
            'iat' => $time,
            'nbf' => $time,
            'exp' => $time+7200,
            'id' => $value['id'],
            'name' => $value['name'],
            'surname' => $value['surname'],
            'email' => $value['email'],
            'role' => $value['role'],
            'image' => $value['image'],
            'active' => $value['active']
        ];
        $alg = 'HS256';
        $token = JWT::encode($payload,$this->key,$alg);
        return $token;
    }

}
