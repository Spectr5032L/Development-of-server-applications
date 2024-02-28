<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Авторизация
Route::post('api/auth/login', [AuthController::class, 'login'])->name('login')->middleware('guest');

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

Route::get('/', function () {
    return view('welcome');
});
