<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LogoutHandler
{
    public function __invoke(Request $request): Response
    {
        try {
            $token = $request->bearerToken();
            $token = JWTAuth::setToken($token)->getToken();
            JWTAuth::manager()->invalidate($token, true);

            return response([
                'message' => 'Cierre de sesión exitoso'
            ], 200);
        } catch (JWTException $exception) {
            return response([
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
