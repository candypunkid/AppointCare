<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\AuthController;
use Modules\User\Http\Controllers\UserController;
use Modules\User\Http\Controllers\TwilioWebhookController;
use Modules\User\Http\Controllers\AppointmentController;
use Modules\User\Http\Controllers\SuperAdminController;
use Modules\User\Http\Controllers\TenantsController;
use Modules\User\Http\Controllers\PlatformUsersController;
use Modules\User\Http\Controllers\TenantAdminController;
use Modules\User\Http\Controllers\TenantUsersController;

// Public appointment booking
Route::get('/book-appointment', [AppointmentController::class, 'showBookingForm'])->name('appointments.book');
Route::post('/book-appointment', [AppointmentController::class, 'submitBookingForm'])->name('appointments.store');

// Twilio webhook routes (public, unguarded)
Route::post('/twilio/incoming-call', [TwilioWebhookController::class, 'handleIncomingCall'])->name('twilio.incoming-call');
Route::post('/twilio/handle-input', [TwilioWebhookController::class, 'handleUserInput'])->name('twilio.handle-input');
Route::post('/twilio/call-status', [TwilioWebhookController::class, 'handleCallStatus'])->name('twilio.call-status');

// Routes requiring session/web state
Route::middleware(['web'])->group(function () {
    // Public auth routes (no guest middleware - handled in controller via Auth::check)
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Authenticated routes
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
        Route::resource('dashboard', UserController::class)->names('dashboard');

        Route::middleware('role:super_admin')->group(function () {
            Route::get('/admin', [SuperAdminController::class, 'index'])->name('admin.dashboard');
            Route::get('/admin/tenants', [TenantsController::class, 'index'])->name('admin.tenants.index');
            Route::get('/admin/tenants/create', [TenantsController::class, 'create'])->name('admin.tenants.create');
            Route::post('/admin/tenants', [TenantsController::class, 'store'])->name('admin.tenants.store');
            Route::get('/admin/tenants/{tenant}/edit', [TenantsController::class, 'edit'])->name('admin.tenants.edit');
            Route::put('/admin/tenants/{tenant}', [TenantsController::class, 'update'])->name('admin.tenants.update');
            Route::get('/admin/users', [PlatformUsersController::class, 'index'])->name('admin.users.index');
            Route::get('/admin/users/{user}/edit', [PlatformUsersController::class, 'edit'])->name('admin.users.edit');
            Route::put('/admin/users/{user}', [PlatformUsersController::class, 'update'])->name('admin.users.update');
            Route::delete('/admin/users/{user}', [PlatformUsersController::class, 'destroy'])->name('admin.users.destroy');
        });

        Route::middleware('role:tenant_admin')->group(function () {
            Route::get('/tenant/dashboard', [TenantAdminController::class, 'index'])->name('tenant.dashboard');
            Route::get('/tenant/users', [TenantUsersController::class, 'index'])->name('tenant.users.index');
            Route::get('/tenant/users/{user}/edit', [TenantUsersController::class, 'edit'])->name('tenant.users.edit');
            Route::put('/tenant/users/{user}', [TenantUsersController::class, 'update'])->name('tenant.users.update');
        });

        Route::post('/appointments/{id}/initiate-call', [TwilioWebhookController::class, 'initiateAppointmentCall'])->name('appointments.initiate-call');
        Route::resource('appointments', AppointmentController::class)->names('appointment');
    });
});
