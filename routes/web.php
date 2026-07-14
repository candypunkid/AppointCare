<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiController;


// Main app routes
Route::get('/', function () {
    $tenant = tenant();

    if ($tenant && auth()->check()) {
        $user = auth()->user();
        if ($user->role === 'tenant_admin') {
            return redirect()->route('tenant.dashboard');
        }
        if ($user->role === 'super_admin') {
            return redirect()->route('admin.dashboard');
        }
    }

    return view('landing');
})->name('home');

// Module routes are automatically registered by Nwidart Modules
// User module routes are loaded from Modules/User/routes/web.php


// AI endpoint for frontpage assistant
Route::post('/ai/respond', [AiController::class, 'respond'])->name('ai.respond')->middleware('throttle:10,1');
