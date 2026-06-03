<?php

use Illuminate\Support\Facades\Route;
use Modules\Appointment\Http\Controllers\AppointmentController;

// Public + tenant aware routes can be placed here
Route::prefix('appointments')->group(function () {
    Route::get('/', [AppointmentController::class, 'index']);
    Route::post('/', [AppointmentController::class, 'store']);
    Route::patch('{id}/reschedule', [AppointmentController::class, 'reschedule']);
    Route::patch('{id}/cancel', [AppointmentController::class, 'cancel']);
});
