<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Api\V1\MfsAgentController;
use App\Http\Controllers\Api\V1\BlacklistController;
use App\Http\Controllers\Api\V1\AuthController;

Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class,'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class,'logout']);
        Route::apiResource('transactions', TransactionController::class)->only(['index','show','store']);
        Route::post('transactions/{id}/verify', [TransactionController::class,'verify'])->middleware('check.ip')->name('transactions.verify');
        Route::post('transactions/{id}/approve', [TransactionController::class,'approve'])->middleware(['check.ip','ensure.totp'])->name('transactions.approve');
        Route::post('transactions/{id}/reject', [TransactionController::class,'reject'])->name('transactions.reject');

        Route::apiResource('mfs-agents', MfsAgentController::class);
        Route::apiResource('blacklist', BlacklistController::class);
    });
});