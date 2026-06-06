<?php

use Illuminate\Support\Facades\Route;

// API routes

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});
