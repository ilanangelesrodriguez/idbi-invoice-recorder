<?php

use App\Http\Controllers\Auth\LoginHandler;
use App\Http\Controllers\Auth\LogoutHandler;
use App\Http\Controllers\Auth\SignUpHandler;
use Illuminate\Support\Facades\Route;

Route::post('/users', SignUpHandler::class);
Route::post('/login', LoginHandler::class);
Route::post('/logout', LogoutHandler::class)
    ->middleware('jwt.verify');
