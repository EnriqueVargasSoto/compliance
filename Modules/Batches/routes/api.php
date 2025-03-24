<?php

use Illuminate\Support\Facades\Route;
use Modules\Batches\Http\Controllers\BatchesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('batches', BatchesController::class)->names('batches');
});
