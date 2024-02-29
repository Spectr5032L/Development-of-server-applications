<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

// Авторизация
Route::post('api/auth/login', [AuthController::class, 'login'])->name('login')->middleware('guest');
// Route::get('/api/auth/login', [AuthController::class, 'login']);

// Регистрация
Route::post('api/auth/register', [AuthController::class, 'register'])->name('register')->middleware('api');

// Получение информации об авторизованном пользователе
Route::get('api/auth/me', [AuthController::class, 'me'])->name('me')->middleware('auth:api');

// Разлогирование
Route::post('api/auth/out', [AuthController::class, 'logout'])->name('logout')->middleware('auth:api');

// Получение списка авторизованных токенов пользователя
Route::get('api/auth/tokens', [AuthController::class, 'tokens'])->name('tokens')->middleware('auth:api');

// Разлогирование всех действующих токенов пользователя
Route::post('api/auth/out_all', [AuthController::class, 'logoutAll'])->name('logout.all')->middleware('auth:api');


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
