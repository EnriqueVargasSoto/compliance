<?php

use Illuminate\Support\Facades\Route;
use Modules\Processes\Http\Controllers\ProcessesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('processes', ProcessesController::class)->names('processes');
});
