<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Attendance\AttendanceController;
use App\Http\Controllers\Attendance\WorkdayController;
use App\Http\Controllers\Attendance\TaskController;
use App\Http\Controllers\Ignug\CatalogueController;

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

Route::group(['prefix' => 'catalogues'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('', [CatalogueController::class, 'filter']);
    });
});
Route::group(['prefix' => 'workdays'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('all', [WorkdayController::class, 'all']);
        Route::get('current_day', [WorkdayController::class, 'getCurrenDate']);
        Route::post('start_day', [WorkdayController::class, 'startDay']);
        Route::put('', [WorkdayController::class, 'update']);
        Route::put('end_day', [WorkdayController::class, 'endDay']);
        Route::delete('{id}', [WorkdayController::class, 'destroy']);
    });
});

Route::group(['prefix' => 'tasks'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('all', [TaskController::class, 'all']);
        Route::get('catalogues', [TaskController::class, 'allCatalogues']);
        Route::get('current_day', [TaskController::class, 'getCurrenDate']);
        Route::get('history', [TaskController::class, 'getHistory']);
        Route::post('', [TaskController::class, 'store']);
        Route::put('', [TaskController::class, 'update']);
        Route::delete('{id}', [TaskController::class, 'destroy']);
    });
});

Route::group(['prefix' => 'attendances'], function () {
//    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('summary', [AttendanceController::class, 'summary']);
        Route::get('detail', [AttendanceController::class, 'detail']);
        Route::get('{id}', [AttendanceController::class, 'show']);
        Route::post('', [AttendanceController::class, 'store']);
        Route::put('', [AttendanceController::class, 'update']);
        Route::delete('{id}', [AttendanceController::class, 'destroy']);
//    });
});


