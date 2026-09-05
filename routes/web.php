<?php

use App\Http\Controllers\Admin\LandingContentController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\CallSimulatorController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PublicBookingController;
use Illuminate\Support\Facades\Route;

// Main app routes
Route::get('/', LandingController::class)->name('home');

// Public booking form
Route::get('/booking', [PublicBookingController::class, 'show'])->name('booking');

// Local AI call simulator (offline testing, exercises the real Twilio webhook pipeline)
Route::get('/call-simulator', [CallSimulatorController::class, 'index'])->name('simulator');
Route::get('/api/simulator/appointment/{appointment}/status', [CallSimulatorController::class, 'appointmentStatus'])->name('simulator.status');

// Module routes are automatically registered by Nwidart Modules
// User module routes are loaded from Modules/User/routes/web.php

// AI endpoint for frontpage assistant
Route::post('/ai/respond', [AiController::class, 'respond'])->name('ai.respond')->middleware('throttle:10,1');

// Super admin landing page content management
Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/landing', [LandingContentController::class, 'index'])->name('landing.index');
    Route::post('/landing/theme', [LandingContentController::class, 'updateTheme'])->name('landing.theme');

    Route::post('/landing/features', [LandingContentController::class, 'storeFeature'])->name('landing.features.store');
    Route::get('/landing/features/{feature}/edit', [LandingContentController::class, 'editFeature'])->name('landing.features.edit');
    Route::put('/landing/features/{feature}', [LandingContentController::class, 'updateFeature'])->name('landing.features.update');
    Route::delete('/landing/features/{feature}', [LandingContentController::class, 'destroyFeature'])->name('landing.features.destroy');

    Route::post('/landing/steps', [LandingContentController::class, 'storeStep'])->name('landing.steps.store');
    Route::get('/landing/steps/{step}/edit', [LandingContentController::class, 'editStep'])->name('landing.steps.edit');
    Route::put('/landing/steps/{step}', [LandingContentController::class, 'updateStep'])->name('landing.steps.update');
    Route::delete('/landing/steps/{step}', [LandingContentController::class, 'destroyStep'])->name('landing.steps.destroy');

    Route::post('/landing/industries', [LandingContentController::class, 'storeIndustry'])->name('landing.industries.store');
    Route::get('/landing/industries/{industry}/edit', [LandingContentController::class, 'editIndustry'])->name('landing.industries.edit');
    Route::put('/landing/industries/{industry}', [LandingContentController::class, 'updateIndustry'])->name('landing.industries.update');
    Route::delete('/landing/industries/{industry}', [LandingContentController::class, 'destroyIndustry'])->name('landing.industries.destroy');

    Route::post('/landing/plans', [LandingContentController::class, 'storePlan'])->name('landing.plans.store');
    Route::get('/landing/plans/{plan}/edit', [LandingContentController::class, 'editPlan'])->name('landing.plans.edit');
    Route::put('/landing/plans/{plan}', [LandingContentController::class, 'updatePlan'])->name('landing.plans.update');
    Route::delete('/landing/plans/{plan}', [LandingContentController::class, 'destroyPlan'])->name('landing.plans.destroy');

    Route::post('/landing/testimonials', [LandingContentController::class, 'storeTestimonial'])->name('landing.testimonials.store');
    Route::get('/landing/testimonials/{testimonial}/edit', [LandingContentController::class, 'editTestimonial'])->name('landing.testimonials.edit');
    Route::put('/landing/testimonials/{testimonial}', [LandingContentController::class, 'updateTestimonial'])->name('landing.testimonials.update');
    Route::delete('/landing/testimonials/{testimonial}', [LandingContentController::class, 'destroyTestimonial'])->name('landing.testimonials.destroy');

    Route::post('/landing/faqs', [LandingContentController::class, 'storeFaq'])->name('landing.faqs.store');
    Route::get('/landing/faqs/{faq}/edit', [LandingContentController::class, 'editFaq'])->name('landing.faqs.edit');
    Route::put('/landing/faqs/{faq}', [LandingContentController::class, 'updateFaq'])->name('landing.faqs.update');
    Route::delete('/landing/faqs/{faq}', [LandingContentController::class, 'destroyFaq'])->name('landing.faqs.destroy');

    Route::post('/landing/stats', [LandingContentController::class, 'storeStat'])->name('landing.stats.store');
    Route::get('/landing/stats/{stat}/edit', [LandingContentController::class, 'editStat'])->name('landing.stats.edit');
    Route::put('/landing/stats/{stat}', [LandingContentController::class, 'updateStat'])->name('landing.stats.update');
    Route::delete('/landing/stats/{stat}', [LandingContentController::class, 'destroyStat'])->name('landing.stats.destroy');
});
