<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Http\Controllers\DashboardController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('dashboard', DashboardController::class)->names('dashboard');
});

Route::get('card-documents', [DashboardController::class, 'quantityDocumentsProcessed'])
    ->name('dashboard.quantityDocumentsProcessed');

Route::get('last-documents', [DashboardController::class, 'latestDocuments'])
    ->name('dashboard.lastDocuments');

Route::get('top-concepts-clients', [DashboardController::class, 'topConceptsClients'])
    ->name('dashboard.topConceptsClients');