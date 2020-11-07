<?php

use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Authentication\UserController;
use App\Http\Controllers\Authentication\PermissionController;
use App\Http\Controllers\Authentication\RouteController;
use App\Http\Controllers\Authentication\ShortcutController;
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

// Users
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::get('logout_all', [AuthController::class, 'logoutAll']);
    Route::post('forgot_password', [AuthController::class, 'forgotPassword']);
    Route::post('reset_password', [AuthController::class, 'resetPassword']);
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::put('password', [AuthController::class, 'changePassword']);
        Route::get('users/permissions', [AuthController::class, 'getPermissions']);
    });
});

//Route::group(['middleware' => 'auth:api'], function () {
Route::apiResource('permissions', PermissionController::class)->middleware('auth:api');
//Route::apiResource('routes', RouteController::class);
Route::get('routes/modules', [RouteController::class, 'getModules']);
Route::get('routes/menus', [RouteController::class, 'getMenus']);
Route::get('routes/mega_menus', [RouteController::class, 'getMegaMenus']);
Route::get('shortcuts', [ShortcutController::class, 'index']);
//});

Route::get('users/export/', [AuthController::class, 'export']);
Route::apiResource('users', UserController::class);// ->middleware('auth:api');
Route::post('users/avatar', [AuthController::class, 'uploadAvatarUri']);
