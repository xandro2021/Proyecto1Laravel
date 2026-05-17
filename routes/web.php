<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\ViewLoginController;
use App\Http\Controllers\Admin\ViewAdminController;
use App\Http\Controllers\User\ViewUserController;

/* Login */

Route::get('/', [ViewLoginController::class, 'home']);

Route::get('/login', [ViewLoginController::class, 'login']);

Route::get('/register', [ViewLoginController::class, 'register']);


/* Admin */

Route::prefix('admin')->group(function () {

    Route::get('/dashboard', [ViewAdminController::class, 'dashboard']);

    Route::get('/catalogo', [ViewAdminController::class, 'catalogo']);

    Route::get('/catalogo/nuevo', [ViewAdminController::class, 'nuevo']);

    Route::get('/catalogo/editar/{id}', [ViewAdminController::class, 'editar']);

    Route::get('/prestamos', [ViewAdminController::class, 'prestamos']);

    Route::get('/prestamos/detalle/{id}', [ViewAdminController::class, 'detallePrestamo']);
});


/* Usuario */

Route::prefix('user')->group(function () {

    Route::get('/catalogo', [ViewUserController::class, 'catalogo']);

    Route::get('/catalogo/solicitud/{id}', [ViewUserController::class, 'solicitud']);

    Route::get('/prestamos', [ViewUserController::class, 'prestamos']);

    Route::get('/prestamos/detalle/{id}', [ViewUserController::class, 'detalle']);
});
