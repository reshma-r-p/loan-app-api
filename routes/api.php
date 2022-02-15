<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\LoanInfoController;
use App\Http\Controllers\API\IssuerController;
use App\Http\Controllers\API\RepaymentInfoController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);


Route::middleware(['auth:sanctum'])->group( function () {

    Route::apiResource('loan-info', LoanInfoController::class);
    Route::post('process-loan', [IssuerController::Class, 'processLoanRequest']);
    Route::post('repayment', [RepaymentInfoController::Class, 'store']);
    Route::get('repayment', [RepaymentInfoController::Class, 'index']);
});


Route::any('{path}', function() {
        return response()->json(['success' => false, 'message' => 'Route not found','data' => ['error' => 'Route not found']], 404);
})->where('path', '.*');