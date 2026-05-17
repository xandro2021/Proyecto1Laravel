<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LoanController;
use App\Http\Controllers\Api\EquipmentController;

/* AUTH */

Route::post('/login', [AuthController::class, 'login']);

Route::post('/register', [AuthController::class, 'register']);


/* PROTEGIDAS */

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    /* EQUIPMENT */

    Route::get('/equipment', [EquipmentController::class, 'index'])
        ->middleware('role:USER,ADMIN');

    Route::get('/equipment/{equipment}', [EquipmentController::class, 'show'])
        ->middleware('role:USER,ADMIN');

    Route::post('/equipment', [EquipmentController::class, 'store'])
        ->middleware('role:ADMIN');

    Route::put('/equipment/{equipment}', [EquipmentController::class, 'update'])
        ->middleware('role:ADMIN');

    Route::delete('/equipment/{equipment}', [EquipmentController::class, 'destroy'])
        ->middleware('role:ADMIN');


    /* LOANS */

    Route::get('/loans', [LoanController::class, 'index'])
        ->middleware('role:ADMIN');

    Route::post('/loans', [LoanController::class, 'store'])
        ->middleware('role:USER,ADMIN');

    Route::put('/loans/{loan}', [LoanController::class, 'update'])
        ->middleware('role:ADMIN');

    Route::delete('/loans/{loan}', [LoanController::class, 'destroy'])
        ->middleware('role:ADMIN');

    Route::get('/loans/{loan}', [LoanController::class, 'show'])
        ->middleware('role:USER,ADMIN');


    /* USERS */

    // Todo users solo ADMIN
    Route::apiResource('users', UserController::class)
        ->middleware('role:ADMIN');
});
