<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmploymentController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Create
//FindAll
//FindOne
//Update
//Delete

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
        Route::post('verify', [UsersController::class, 'verify']);

        
    });
    
    Route::group([
        'prefix' => 'roles'
    ], function ($router) {
        Route::post('create', [RolesController::class, 'Create']);
        Route::get('getAll', [RolesController::class, 'FindAll']);
        Route::get('getOne/{uuid}', [RolesController::class, 'FindOne']);
        Route::put('update/{uuid}', [RolesController::class, 'Update']);
    });

    Route::group([
        'prefix' => 'employment'
    ], function ($router) {
        Route::post('create', [EmploymentController::class, 'Create']);
        Route::get('getAll', [EmploymentController::class, 'FindAll']);
        Route::get('getOne/{uuid}', [EmploymentController::class, 'FindOne']);
        Route::put('update/{uuid}', [EmploymentController::class, 'Update']);
    });

});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
