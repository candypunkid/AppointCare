<?php

use Illuminate\Support\Facades\Route;

// Main app routes
Route::get('/', function () {
    $tenant = tenant();

    if ($tenant) {
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->role === 'tenant_admin') {
                return redirect()->route('tenant.dashboard');
            }
            if ($user->role === 'super_admin') {
                return redirect()->route('admin.dashboard');
            }
        }

        return redirect()->route('appointments.book');
    }

    // Always show the main landing page for non-tenant hosts, even if the user is logged in.
    return view('landing');
})->name('home');

// Module routes are automatically registered by Nwidart Modules
// User module routes are loaded from Modules/User/routes/web.php

use App\Http\Controllers\AiController;

// AI endpoint for frontpage assistant
Route::post('/ai/respond', [AiController::class, 'respond'])->name('ai.respond')->middleware('throttle:10,1');
