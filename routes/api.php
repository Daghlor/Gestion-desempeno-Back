<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'public'
], function ($router) {
    Route::post('register', [UsersController::class, 'registerPublic']);
});

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::group([
        'prefix' => 'users'
    ], function ($router) {
        Route::post('register', [UsersController::class, 'registerAdmin']);
        Route::post('getAll', [UsersController::class, 'findAll']);
        Route::get('getOne/{uuid}', [UsersController::class, 'findOne']);
        Route::put('update/{uuid}', [UsersController::class, 'update']);
        Route::delete('delete/{uuid}', [UsersController::class, 'delete']);
    });
    
    
    Route::group([
        'prefix' => 'roles'
    ], function ($router) {
        Route::post('', [RolesController::class, '']);
    });

});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
