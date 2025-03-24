<?php

use Illuminate\Support\Facades\Route;
use Modules\Security\Http\Controllers\SecurityController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('security', SecurityController::class)->names('security');
});
