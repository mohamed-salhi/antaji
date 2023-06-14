<?php

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
Route::get('/intros', [\App\Http\Controllers\Api\AuthController::class, 'intros']);

Route::middleware(['guest:sanctum'])->prefix('auth')->group(function () {
    Route::get('/first', [\App\Http\Controllers\Api\AuthController::class, 'first']);
    Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('/verify_code', [\App\Http\Controllers\Api\AuthController::class, 'verifyCode']);
    Route::post('/again', [\App\Http\Controllers\Api\AuthController::class, 'again']);
    Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);

});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout/{fcm?}/{token?}', [\App\Http\Controllers\Api\AuthController::class, 'logout']);

});
