<?php

use Modules\Appointment\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;

// Public: customer form submission
Route::post(''/appointments'', [AppointmentController::class, ''store'']);
Route::get(''/appointments/{appointment}'', [AppointmentController::class, ''show'']);

// Admin: manage appointments (protect with auth middleware)
Route::middleware([''auth:sanctum'']).prefix(''admin'').group(function () {
    Route::get(''/appointments'', [AppointmentController::class, ''index'']);
    Route::put(''/appointments/{appointment}'', [AppointmentController::class, ''update'']);
    Route::delete(''/appointments/{appointment}'', [AppointmentController::class, ''destroy'']);
    Route::post(''/appointments/{appointment}/retry-call'', [AppointmentController::class, ''retryCall'']);
});
