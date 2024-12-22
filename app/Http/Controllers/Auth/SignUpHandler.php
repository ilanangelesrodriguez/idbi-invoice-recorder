<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\SignUpRequest;
use App\Http\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Http\Response;

class SignUpHandler
{
    public function __invoke(SignUpRequest $request): Response
    {
        $user = User::create([
            'name' => $request->input('name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        return response([
            'data' => UserResource::make($user)
        ], 201);
    }
}
