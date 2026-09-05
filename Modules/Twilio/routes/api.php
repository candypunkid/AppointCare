<?php

use Illuminate\Support\Facades\Route;
use Modules\Twilio\Http\Controllers\TwilioWebhookController;

Route::prefix('twilio')->withoutMiddleware(['csrf', 'auth'])->group(function () {
    Route::post('/webhook/stream', [TwilioWebhookController::class, 'handleStream']);
    Route::post('/webhook/status', [TwilioWebhookController::class, 'handleStatus']);
    Route::get('/health', [TwilioWebhookController::class, 'health']);
});
