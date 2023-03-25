<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmploymentController;
use App\Http\Controllers\ObjectivesIndividualController;
use App\Http\Controllers\ObjectivesStrategicsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\TracingController;
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
    Route::get('findData', [AuthController::class, 'findData']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'public'
], function ($router) {
    Route::post('register', [UsersController::class, 'registerPublic']);
    Route::get('employments', [EmploymentController::class, 'FindAllPublic']);
    Route::get('companies', [CompanyController::class, 'FindAllPublic']);
});

Route::group(['middleware' => ['jwt.verify']], function() {

    Route::group([
        'prefix' => 'auth'
    ], function ($router) {
        Route::get('findData', [AuthController::class, 'findData']);
    });

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
        Route::post('getAll', [EmploymentController::class, 'FindAll']);
        Route::get('getOne/{uuid}', [EmploymentController::class, 'FindOne']);
        Route::put('update/{uuid}', [EmploymentController::class, 'Update']);
        Route::delete('delete/{uuid}', [EmploymentController::class, 'Delete']);
    });

    Route::group([
        'prefix' => 'company'
    ], function ($router) {
        Route::post('create', [CompanyController::class, 'Create']);
        Route::post('getAll', [CompanyController::class, 'FindAll']);
        Route::get('getOne/{uuid}', [CompanyController::class, 'FindOne']);
        Route::put('update/{uuid}', [CompanyController::class, 'Update']);
        Route::delete('delete/{uuid}', [CompanyController::class, 'Delete']);
    });

    Route::group([
        'prefix' => 'area'
    ], function ($router) {
        Route::post('create', [AreaController::class, 'Create']);
        Route::post('getAll', [AreaController::class, 'FindAll']);
        Route::get('getOne/{uuid}', [AreaController::class, 'FindOne']);
        Route::put('update/{uuid}', [AreaController::class, 'Update']);
        Route::delete('delete/{uuid}', [AreaController::class, 'Delete']);
    });

    Route::group([
        'prefix' => 'strategics'
    ], function ($router) {
        Route::post('create', [ObjectivesStrategicsController::class, 'Create']);
        Route::post('getAll', [ObjectivesStrategicsController::class, 'FindAll']);
        Route::get('getOne/{uuid}', [ObjectivesStrategicsController::class, 'FindOne']);
        Route::delete('delete/{uuid}', [ObjectivesStrategicsController::class, 'Delete']);
    });

    Route::group([
        'prefix' => 'individuals'
    ], function ($router) {
        Route::post('create', [ObjectivesIndividualController::class, 'Create']);
        Route::post('getAll', [ObjectivesIndividualController::class, 'FindAll']);
        Route::get('getOne/{uuid}', [ObjectivesIndividualController::class, 'FindOne']);
    });

    Route::group([
        'prefix' => 'tracing'
    ], function ($router) {
        Route::post('create', [TracingController::class, 'Create']);
        Route::post('getAll', [TracingController::class, 'FindAll']);
        Route::get('getOne/{uuid}', [TracingController::class, 'FindOne']);
    });

});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
