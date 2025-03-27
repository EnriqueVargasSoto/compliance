<?php

use Illuminate\Support\Facades\Route;
use Modules\Processes\Http\Controllers\ProcessesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('processes', ProcessesController::class)->names('processes');
});
