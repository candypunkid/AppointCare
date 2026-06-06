<?php

use Modules\Twilio\Http\Controllers\TwilioWebhookController;
use Illuminate\Support\Facades\Route;

// These routes are called by Twilio and must skip CSRF and auth
Route::prefix(''twilio'')->withoutMiddleware([''csrf'', ''auth''])->group(function () {
    Route::post(''/webhook/stream'', [TwilioWebhookController::class, ''handleStream'']);
    Route::post(''/webhook/status'', [TwilioWebhookController::class, ''handleStatus'']);
    Route::get(''/health'', [TwilioWebhookController::class, ''health'']);
});
