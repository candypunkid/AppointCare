<?php

use App\Http\Controllers\AppointmentAIController;
use App\Http\Controllers\TwilioController;
use App\Http\Controllers\VoiceWebhookController;
use Illuminate\Support\Facades\Route;

// Health check
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// Twilio webhook routes (no CSRF, no auth - called by Twilio)
Route::prefix('twilio')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->group(function () {
    Route::post('/voice', [VoiceWebhookController::class, 'handleVoice'])->name('api.twilio.voice');
    Route::post('/status', [VoiceWebhookController::class, 'handleStatus'])->name('api.twilio.status');
    Route::post('/outbound-call', [VoiceWebhookController::class, 'handleOutboundCall'])->name('api.twilio.outbound');
    Route::post('/incoming-call', [VoiceWebhookController::class, 'handleIncomingCall'])->name('api.twilio.incoming');
    Route::post('/gather', [VoiceWebhookController::class, 'handleGather'])->name('api.twilio.gather');
});

// Public booking endpoint - submits form & triggers AI call
Route::post('/book-and-call', [AppointmentAIController::class, 'bookAndCall'])->name('api.book-and-call');

// OpenAI analysis endpoint
Route::post('/openai/analyze', [AppointmentAIController::class, 'analyzeIntent'])->name('api.openai.analyze');

// AI appointment management (protected)
Route::middleware(['auth:sanctum'])->prefix('ai')->group(function () {
    Route::post('/appointments/initiate-call', [AppointmentAIController::class, 'triggerReminderCall'])->name('api.ai.trigger-call');
    Route::get('/appointments/{appointment}/conversations', [AppointmentAIController::class, 'conversationHistory'])->name('api.ai.conversations');
    Route::get('/appointments/availability', [AppointmentAIController::class, 'checkAvailability'])->name('api.ai.availability');
    Route::get('/appointments/slots', [AppointmentAIController::class, 'availableSlots'])->name('api.ai.slots');
});

// Twilio management (protected)
Route::middleware(['auth:sanctum'])->prefix('twilio')->group(function () {
    Route::post('/initiate-call', [TwilioController::class, 'initiateOutboundCall'])->name('api.twilio.initiate');
    Route::post('/send-sms', [TwilioController::class, 'sendSMSNotification'])->name('api.twilio.send-sms');
    Route::get('/call-logs', [TwilioController::class, 'callLogs'])->name('api.twilio.call-logs');
});

// Dashboard analytics (protected)
Route::middleware(['auth:sanctum'])->get('/dashboard/analytics', [AppointmentAIController::class, 'dashboardAnalytics'])->name('api.dashboard.analytics');
