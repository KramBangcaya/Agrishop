<?php

use App\Http\Controllers\ProductsController;
use App\Models\Products;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {

    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function () {

    Route::group(['prefix' => 'product'], function () {
        Route::get('list', [App\Http\Controllers\ProductsController::class, 'index']);
        Route::get('all', [App\Http\Controllers\ProductsController::class, 'index_all']);
        Route::post('create', [App\Http\Controllers\ProductsController::class, 'store']);
        Route::post('update/{id}', [App\Http\Controllers\ProductsController::class, 'update']);
        Route::delete('delete/{id}', [App\Http\Controllers\ProductsController::class, 'destroy']);
    });
    Route::group(['prefix' => 'Report'], function () {
        Route::get('list', [App\Http\Controllers\ReportController::class, 'index']);
        Route::post('create', [App\Http\Controllers\ReportController::class, 'store']);
        Route::put('response', [App\Http\Controllers\ReportController::class, 'response']);
        Route::delete('delete/{id}', [App\Http\Controllers\ReportController::class, 'destroy']);
        Route::get('list_all', [App\Http\Controllers\ReportController::class, 'index_all']);
    });
    Route::group(['prefix' => 'replenishment'], function () {
        Route::get('list', [App\Http\Controllers\ProductsController::class, 'index1']);
        Route::post('create', [App\Http\Controllers\ProductsController::class, 'store1']);
        Route::post('update/{id}', [App\Http\Controllers\ProductsController::class, 'update1']);
        Route::delete('delete/{id}', [App\Http\Controllers\ProductsController::class, 'destroy1']);
    });

    Route::group(['prefix' => 'category'], function () {
        Route::get('list', [App\Http\Controllers\CategoryController::class, 'index']);
        Route::get('all', [App\Http\Controllers\CategoryController::class, 'index_all']);
        Route::get('category', [App\Http\Controllers\CategoryController::class, 'all']);
        Route::post('create', [App\Http\Controllers\CategoryController::class, 'store']);
        Route::put('update/{id}', [App\Http\Controllers\CategoryController::class, 'update']);
        Route::delete('delete/{id}', [App\Http\Controllers\CategoryController::class, 'destroy']);
    });
    Route::group(['prefix' => 'measurement'], function () {
        Route::get('list', [App\Http\Controllers\MeasurementController::class, 'index']);
        Route::get('all', [App\Http\Controllers\MeasurementController::class, 'index_all']);
        Route::post('create', [App\Http\Controllers\MeasurementController::class, 'store']);
        Route::put('update/{id}', [App\Http\Controllers\MeasurementController::class, 'update']);
        Route::delete('delete/{id}', [App\Http\Controllers\MeasurementController::class, 'destroy']);
    });
    Route::group(['prefix' => 'user'], function () {
        Route::get('list', [App\Http\Controllers\API\UserController::class, 'index']);
        Route::get('all', [App\Http\Controllers\API\UserController::class, 'index_all']);
        Route::get('show', [App\Http\Controllers\API\UserController::class, 'show']);
        Route::post('create', [App\Http\Controllers\API\UserController::class, 'store']);
        Route::put('validate/{id}', [App\Http\Controllers\API\UserController::class, 'validated']);
        Route::post('update', [App\Http\Controllers\API\UserController::class, 'update']);
        Route::put('assign', [App\Http\Controllers\API\UserController::class, 'assign']);
        Route::delete('delete/{id}', [App\Http\Controllers\API\UserController::class, 'destroy']);
        Route::put('activate/{id}', [App\Http\Controllers\API\UserController::class, 'activate']);
        Route::post('register', [App\Http\Controllers\API\UserController::class, 'register']);
    });
    Route::group(['prefix' => 'deliquency'], function () {
        Route::get('list', [App\Http\Controllers\API\DeliquencyController::class, 'index']);
        Route::get('show', [App\Http\Controllers\API\UserController::class, 'show']);
        Route::post('create', [App\Http\Controllers\API\UserController::class, 'store']);
        Route::put('validate/{id}', [App\Http\Controllers\API\UserController::class, 'validated']);
        Route::put('update', [App\Http\Controllers\API\UserController::class, 'update']);
        Route::delete('delete/{id}', [App\Http\Controllers\API\UserController::class, 'destroy']);
        Route::put('activate/{id}', [App\Http\Controllers\API\UserController::class, 'activate']);
    });
    Route::group(['prefix' => 'permission'], function () {
        Route::get('list', [App\Http\Controllers\API\PermissionController::class, 'index']);
        Route::get('all', [App\Http\Controllers\API\PermissionController::class, 'index_all']);
        Route::post('create', [App\Http\Controllers\API\PermissionController::class, 'store']);
        Route::put('update/{id}', [App\Http\Controllers\API\PermissionController::class, 'update']);
        Route::delete('delete/{id}', [App\Http\Controllers\API\PermissionController::class, 'destroy']);
    });
    Route::group(['prefix' => 'role'], function () {
        Route::get('list', [App\Http\Controllers\API\RoleController::class, 'index']);
        Route::get('all', [App\Http\Controllers\API\RoleController::class, 'index_all']);
        Route::post('create', [App\Http\Controllers\API\RoleController::class, 'store']);
        Route::put('update/{id}', [App\Http\Controllers\API\RoleController::class, 'update']);
        Route::delete('delete/{id}', [App\Http\Controllers\API\RoleController::class, 'destroy']);
    });
});


Route::group(['prefix' => 'product1'], function () {
    Route::get('all', [App\Http\Controllers\ProductsController::class, 'index_all']);
});
