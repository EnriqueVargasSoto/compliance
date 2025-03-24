<?php

use Illuminate\Support\Facades\Route;
use Modules\Security\Http\Controllers\CompanyController;
use Modules\Security\Http\Controllers\ModuleController;
use Modules\Security\Http\Controllers\OfficeController;
use Modules\Security\Http\Controllers\PermissionController;
use Modules\Security\Http\Controllers\PersonController;
use Modules\Security\Http\Controllers\RoleController;
use Modules\Security\Http\Controllers\SecurityController;
use Modules\Security\Http\Controllers\UserController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('security', SecurityController::class)->names('security');
});

Route::middleware('auth:api')->group(function () {
    Route::apiResource('companies', CompanyController::class)->names('companies');
    Route::get('companies-init-table', [CompanyController::class, 'initTable'])->name('companies.initTable');

    Route::apiResource('offices', OfficeController::class)->names('offices');
    Route::get('offices-init-table', [OfficeController::class, 'initTable'])->name('offices.initTable');

    Route::apiResource('modules', ModuleController::class)->names('modules');
    Route::get('modules-init-table', [ModuleController::class, 'initTable'])->name('modules.initTable');

    Route::apiResource('people', PersonController::class)->names('people');
    Route::get('people-init-table', [PersonController::class, 'initTable'])->name('people.initTable');

    Route::apiResource('roles', RoleController::class)->names('roles');
    Route::get('roles-init-table', [RoleController::class, 'initTable'])->name('roles.initTable');

    Route::apiResource('permissions', PermissionController::class)->names('permissions');
    Route::get('permissions-init-table', [PermissionController::class, 'initTable'])->name('permissions.initTable');

    Route::apiResource('users', UserController::class)->names('users');
    Route::get('users-init-table', [UserController::class, 'initTable'])->name('users.initTable');
});


