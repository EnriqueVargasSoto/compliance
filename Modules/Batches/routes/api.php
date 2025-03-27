<?php

use Illuminate\Support\Facades\Route;
use Modules\Batches\Http\Controllers\BatchesController;

Route::middleware('auth:api')->group(function () {
    Route::apiResource('batches', BatchesController::class)->names('batches');
    Route::get('batches-init-table', [BatchesController::class, 'initTable'])->name('companies.initTable');
});
