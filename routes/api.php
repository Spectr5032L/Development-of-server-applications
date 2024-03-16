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


// смотри скрины от 29-го февраля (Microsoft Edge выбери изменить или повторить отправку)
// 1) писать пути в api (использовать группу маршрутов для одного перфикса)

// Затрагиваемые файлы:
// LoginRequest.php
// RegisterRequest.php
// AuthResourceDTO.php
// RegisterResourceDTO.php
// UserResourceDTO.php
// AuthController.php
// auth.php
// users.php




// Авторизация
Route::group(['prefix' => 'auth/'], function () {
    // нужно скачать postman чтобы работать через запросы с api
    Route::post('/login', [AuthController::class, 'login']); // не работает logreq
    Route::post('/register', function () {
        print_r($_POST);
        // return $request->all();
    }); // не работает logreq
    // Route::post('/register', 'AuthController@register'); // не работает logreq
});
// Route::post('api/auth/login', [AuthController::class, 'login'])->name('login')->middleware('guest');
// C:\OSPanel\domains\Development-of-server-applications\app\Http\Controllers\AuthController.php
// Development-of-server-applications\app\Http\Controllers\AuthController.php

// // Регистрация
// Route::post('api/auth/register', [AuthController::class, 'register'])->name('register')->middleware('api');

// // Получение информации об авторизованном пользователе
// Route::get('api/auth/me', [AuthController::class, 'me'])->name('me')->middleware('auth:api');

// // Разлогирование
// Route::post('api/auth/out', [AuthController::class, 'logout'])->name('logout')->middleware('auth:api');

// // Получение списка авторизованных токенов пользователя
// Route::get('api/auth/tokens', [AuthController::class, 'tokens'])->name('tokens')->middleware('auth:api');

// // Разлогирование всех действующих токенов пользователя
// Route::post('api/auth/out_all', [AuthController::class, 'logoutAll'])->name('logout.all')->middleware('auth:api');


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
