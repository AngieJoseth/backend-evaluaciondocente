<?php

use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Authentication\UserController;
use App\Http\Controllers\Authentication\PermissionController;
use App\Http\Controllers\Authentication\RouteController;
use App\Http\Controllers\Authentication\ShortcutController;
use Illuminate\Support\Facades\Route;

// Authentication
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::get('logout_all', [AuthController::class, 'logoutAll']);
    Route::post('forgot_password', [AuthController::class, 'forgotPassword']);
    Route::post('reset_password', [AuthController::class, 'resetPassword']);
//    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::put('change_password', [AuthController::class, 'changePassword']);
//    });
});

//
//Route::group(['middleware' => 'auth:api'], function () {
Route::apiResource('permissions', PermissionController::class)->middleware('auth:api');
Route::apiResource('routes', RouteController::class);
Route::apiResource('shortcuts', ShortcutController::class);
Route::apiResource('users', UserController::class)->except(['index']);// ->middleware('auth:api');
Route::post('users/filters', [UserController::class, 'index']);// ->middleware('auth:api');
Route::post('users/avatar', [UserController::class, 'uploadAvatarUri']);
Route::get('users/export/', [UserController::class, 'export']);
//});




