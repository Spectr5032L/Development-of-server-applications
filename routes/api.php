<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserAndRoleController;
use App\Http\Controllers\RoleAndPermissionController;
use App\Http\Controllers\UserController;

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

Route::prefix('ref/policy/')->middleware(['auth:sanctum', 'check.permissions'])->group(function () {
    
    Route::prefix('role/')->group(function () {
        Route::get('', [RoleController::class, 'getAll']);
        Route::post('', [RoleController::class, 'create']);

        Route::prefix('{id}/')->group(function () {
            Route::get('', [RoleController::class, 'getById']);
            Route::put('', [RoleController::class, 'update']);
            Route::delete('', [RoleController::class, 'hardDelete']);
            Route::delete('soft', [RoleController::class, 'softDelete']);
            Route::post('restore', [RoleController::class, 'restore']);
        });
    });

    Route::prefix('permission/')->group(function () {
        Route::get('', [PermissionController::class, 'getAll']);
        Route::post('', [PermissionController::class, 'create']);

        Route::prefix('{id}/')->group(function () {
            Route::get('', [PermissionController::class, 'getById']);
            Route::put('', [PermissionController::class, 'update']);
            Route::delete('', [PermissionController::class, 'hardDelete']);
            Route::delete('soft', [PermissionController::class, 'softDelete']);
            Route::post('restore', [PermissionController::class, 'restore']);
        });
    });

    Route::prefix('userAndRole/')->group(function () {
        Route::get('', [UserAndRoleController::class, 'getAll']);
        Route::post('', [UserAndRoleController::class, 'create']);

        Route::prefix('{id}/')->group(function () {
            Route::get('', [UserAndRoleController::class, 'getById']);
            Route::put('', [UserAndRoleController::class, 'update']);
            Route::delete('', [UserAndRoleController::class, 'hardDelete']);
            Route::delete('soft', [UserAndRoleController::class, 'softDelete']);
            Route::post('restore', [UserAndRoleController::class, 'restore']);
        });
    });

    Route::prefix('roleAndPermission/')->group(function () {
        Route::get('', [RoleAndPermissionController::class, 'getAll']);
        Route::post('', [RoleAndPermissionController::class, 'create']);

        Route::prefix('{id}/')->group(function () {
            Route::get('', [RoleAndPermissionController::class, 'getById']);
            Route::put('', [RoleAndPermissionController::class, 'update']);
            Route::delete('', [RoleAndPermissionController::class, 'hardDelete']);
            Route::delete('soft', [RoleAndPermissionController::class, 'softDelete']);
            Route::post('restore', [RoleAndPermissionController::class, 'restore']);
        });
    });

});

Route::prefix('ref/')->middleware(['auth:sanctum', 'check.permissions'])->group(function () {

    Route::prefix('user/')->group(function () {
        Route::get('', [UserController::class, 'getAll']);
        Route::post('', [UserController::class, 'create']);

        Route::prefix('{id}/')->group(function () {
            Route::get('', [UserController::class, 'getById']);
            Route::put('', [UserController::class, 'update']);
            Route::delete('', [UserController::class, 'hardDelete']);
            Route::delete('soft', [UserController::class, 'softDelete']);
            Route::post('restore', [UserController::class, 'restore']);
        });
    });

});


























// $map = [
//     'UserController' => 'user',
//     'RoleController' => 'role',
//     'PermissionController' => 'permission',
//     'UserAndRoleController' => 'userAndRole',
//     'RoleAndPermissionController' => 'roleAndPermission',
// ];