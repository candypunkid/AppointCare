<?php

use Illuminate\Support\Facades\Route;
use Modules\AICall\Http\Controllers\AICallController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('aicalls', AICallController::class)->names('aicall');
});
