<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\PermisoRolController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\RolUserController;
use App\Http\Controllers\TipoPermisoController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\VerifyPermission;
use App\Models\PermisoRol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('auth/login', [AuthController::class, 'login']);
Route::prefix('auth')->middleware(['jwt.verify'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});
Route::prefix('password')->group(function () {
    Route::post('create', [PasswordResetController::class, 'create']);
    Route::get('find/{token}', [PasswordResetController::class, 'find']);
    Route::post('reset', [PasswordResetController::class, 'reset']);
});
Route::prefix('tipo_permiso')->group(function () {
    Route::get('/', [TipoPermisoController::class, 'index'])->middleware(['jwt.verify','verify.permiso:LIST_TIPO_PERMISO']);
    Route::post('/', [TipoPermisoController::class, 'store'])->middleware(['jwt.verify','verify.permiso:CREATE_TIPO_PERMISO']);
    Route::put('/{tipo_permiso}', [TipoPermisoController::class, 'update'])->middleware(['jwt.verify','verify.permiso:UPDATE_TIPO_PERMISO']);
    Route::delete('/{tipo_permiso}', [TipoPermisoController::class, 'destroy'])->middleware(['jwt.verify','verify.permiso:DESTROY_TIPO_PERMISO']);
});
Route::prefix('permiso')->group(function () {
    Route::get('/', [PermisoController::class, 'index'])->middleware(['jwt.verify','verify.permiso:LIST_PERMISO']);
    Route::post('/', [PermisoController::class, 'store'])->middleware(['jwt.verify','verify.permiso:CREATE_PERMISO']);
    Route::put('/{permiso}', [PermisoController::class, 'update'])->middleware(['jwt.verify','verify.permiso:UPDATE_PERMISO']);
    Route::delete('/{permiso}', [PermisoController::class, 'destroy'])->middleware(['jwt.verify','verify.permiso:DESTROY_PERMISO']);
});

Route::prefix('rol')->group(function () {
    Route::get('/', [RolController::class, 'index'])->middleware(['jwt.verify','verify.permiso:LIST_ROL']);
    Route::get('/{rol}', [RolController::class, 'show'])->middleware(['jwt.verify','verify.permiso:SHOW_ROL']);
    Route::post('/', [RolController::class, 'store'])->middleware(['jwt.verify','verify.permiso:CREATE_ROL']);
    Route::put('/{rol}', [RolController::class, 'update'])->middleware(['jwt.verify','verify.permiso:UPDATE_ROL']);
    Route::delete('/{rol}', [RolController::class, 'destroy'])->middleware(['jwt.verify','verify.permiso:DESTROY_ROL']);
});

Route::prefix('permiso_rol')->group(function () {
    Route::post('/', [PermisoRolController::class, 'store'])->middleware(['jwt.verify','verify.permiso:PERMISO_ROL_UPDATE']);
});

Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'index'])->middleware(['jwt.verify','verify.permiso:LIST_USUARIO']);
    Route::get('/{user}', [UserController::class, 'show'])->middleware(['jwt.verify','verify.permiso:SHOW_USUARIO']);
    Route::post('/', [UserController::class, 'store'])->middleware(['jwt.verify','verify.permiso:CREATE_USUARIO']);
    Route::put('/{user}', [UserController::class, 'update'])->middleware(['jwt.verify','verify.permiso:UPDATE_USUARIO']);
    Route::delete('/{user}', [UserController::class, 'destroy'])->middleware(['jwt.verify','verify.permiso:DESTROY_USUARIO']);
});
Route::prefix('rol_user')->group(function () {
    Route::post('/', [RolUserController::class, 'store'])->middleware(['jwt.verify','verify.permiso:ROL_USER_UPDATE']);
});
