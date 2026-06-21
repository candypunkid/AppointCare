<?php

use Illuminate\Support\Facades\Route;
use Modules\Twilio\Http\Controllers\TwilioController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('twilios', TwilioController::class)->names('twilio');
});

Route::get('/send-sms', [TwilioController::class, 'sendSms']);
