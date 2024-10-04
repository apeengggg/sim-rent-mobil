<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MerekMobilApiController;
use App\Http\Controllers\Api\MobilApiController;
use App\Http\Controllers\Api\RoleApiController;
use App\Http\Controllers\Api\UserApiController;
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

Route::prefix('v1')->group(function () {
    // 
    Route::middleware(['auth.filter'])->prefix('users')->group(function () {
        Route::get('/', [UserApiController::class, 'index']);
        Route::get('/{id}', [UserApiController::class, 'getById']);
        Route::post('/', [UserApiController::class, 'store']);
        Route::put('/', [UserApiController::class, 'update']);
        Route::delete('/', [UserApiController::class, 'destroy']);
    });

    Route::middleware(['auth.filter'])->prefix('merek-mobils')->group(function () {
        Route::get('/', [MerekMobilApiController::class, 'index']);
        Route::get('/{id}', [MerekMobilApiController::class, 'getById']);
        Route::post('/', [MerekMobilApiController::class, 'store']);
        Route::put('/', [MerekMobilApiController::class, 'update']);
    });

    Route::middleware(['auth.filter'])->prefix('mobils')->group(function () {
        Route::get('/', [MobilApiController::class, 'index']);
        Route::get('/{id}', [MobilApiController::class, 'getById']);
        Route::post('/', [MobilApiController::class, 'store']);
        Route::put('/', [MobilApiController::class, 'update']);
        Route::delete('/', [MobilApiController::class, 'destroy']);
    });

    Route::prefix('roles')->group(function () {
        Route::get('/all', [RoleApiController::class, 'all']);
    });

    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
    });
});