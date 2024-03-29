<?php

use App\Http\Controllers\V1\AccountTransferController;
use App\Http\Controllers\V1\TransactionTopUsageController;
use App\Http\Middleware\SanitizeNumbers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('accounts/transfer', AccountTransferController::class)
    ->middleware(SanitizeNumbers::class)
    ->name('account.transfer');

Route::get('transactions/usage', TransactionTopUsageController::class)->name('txn.usage');
