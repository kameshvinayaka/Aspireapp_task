<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\LoanController;
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

Route::post('login',[UserController::class,'login']);

// authorised apis
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'loan'], function () {
        Route::post('/create',[LoanController::class,'create']);
        Route::post('/list',[LoanController::class,'list']);
        Route::post('/details',[LoanController::class,'details']);
        Route::post('/proccess',[LoanController::class,'proccess']);
        Route::post('/repayment',[LoanController::class,'repayment']);
    });
});