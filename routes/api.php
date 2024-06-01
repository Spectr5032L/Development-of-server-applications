<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MajorController;

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


// password: the2fAt$rat

Route::prefix('auth')->group(function () {

    Route::post('/login', [MajorController::class, 'login'])->name('login');

    Route::middleware('guest:sanctum')->post('/register', [MajorController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [MajorController::class, 'infUser'])->name('me');
        Route::post('/out', [MajorController::class, 'out']);
        Route::get('/tokens', [MajorController::class, 'tokens']);
        Route::post('/out_all', [MajorController::class, 'outAll']);
    });

});
