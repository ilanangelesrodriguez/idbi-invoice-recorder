<?php

use Illuminate\Support\Facades\Route;

include_once 'v1/no-auth.php';

Route::group(['middleware' => ['jwt.verify']], function () {
    include_once 'v1/auth.php';
});
