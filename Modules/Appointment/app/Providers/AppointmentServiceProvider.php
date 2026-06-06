<?php

namespace Modules\Appointment\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Appointment\Models\Appointment;
use Modules\Appointment\Observers\AppointmentObserver;

class AppointmentServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Appointment::observe(AppointmentObserver::class);
    }
}
