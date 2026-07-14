<?php

use App\Jobs\MakeReminderCallJob;
use App\Models\Appointment;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Dispatch reminder calls 24 hours before appointment
Schedule::call(function () {
    $target = now()->addDay();
    $appointments = Appointment::with('customer')
        ->whereDate('scheduled_at', $target->toDateString())
        ->whereTime('scheduled_at', '>=', $target->copy()->subHour()->format('H:i:s'))
        ->whereTime('scheduled_at', '<=', $target->copy()->addHour()->format('H:i:s'))
        ->whereIn('status', ['pending', 'confirmed'])
        ->whereHas('customer', fn ($q) => $q->whereNotNull('phone'))
        ->get();

    foreach ($appointments as $appointment) {
        MakeReminderCallJob::dispatch($appointment);
    }
})->name('reminders.24h')->hourly();

// Dispatch reminder calls 2 hours before appointment
Schedule::call(function () {
    $target = now()->addHours(2);
    $appointments = Appointment::with('customer')
        ->whereDate('scheduled_at', $target->toDateString())
        ->whereTime('scheduled_at', '>=', $target->copy()->subMinutes(30)->format('H:i:s'))
        ->whereTime('scheduled_at', '<=', $target->copy()->addMinutes(30)->format('H:i:s'))
        ->whereIn('status', ['pending', 'confirmed'])
        ->whereHas('customer', fn ($q) => $q->whereNotNull('phone'))
        ->get();

    foreach ($appointments as $appointment) {
        MakeReminderCallJob::dispatch($appointment);
    }
})->name('reminders.2h')->everyThirtyMinutes();
