<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(
    function () {
        include_once 'vouchers/auth.php';
    }
);
