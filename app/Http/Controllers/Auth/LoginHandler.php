<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Users\UserResource;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginHandler
{
    public function __invoke(LoginRequest $request): Response
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response([
                    'message' => 'Credenciales inválidas'
                ], 401);
            }
        } catch (JWTException $exception) {
            return response([
                'message' => 'No se pudo crear el token'
            ], 500);
        }

        $user = JWTAuth::user();

        return response([
            'data' => [
                'token' => $token,
                'user' => UserResource::make($user),
            ],
        ], 200);
    }
}
