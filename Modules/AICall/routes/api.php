<?php

use Illuminate\Support\Facades\Route;
use Modules\AICall\Http\Controllers\AICallController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('aicalls', AICallController::class)->names('aicall');
});
