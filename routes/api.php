<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('/users', UserController::class);
Route::apiResource('/products', ProductController::class);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::apiResource('/orders', OrderController::class);

    Route::post('/logout', [AuthController::class, 'logout']);
});
