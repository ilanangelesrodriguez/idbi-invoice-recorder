<?php

use App\Http\Controllers\VoucherController;
use App\Http\Controllers\Vouchers\GetVouchersHandler;
use App\Http\Controllers\Vouchers\StoreVouchersHandler;
use Illuminate\Support\Facades\Route;

Route::prefix('vouchers')->group(
    function () {
        Route::get('/', GetVouchersHandler::class);
        Route::post('/', StoreVouchersHandler::class);
        Route::post('/register', [VoucherController::class, 'registerInvoices']);
        Route::get('/totals', [VoucherController::class, 'getTotalAmountsByCurrency']);
        Route::delete('/{id}', [VoucherController::class, 'deleteVoucher']);
    }
);
