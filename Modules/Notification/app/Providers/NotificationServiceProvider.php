<?php

namespace Modules\Notification\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Modules\Appointment\Events\AppointmentStatusChanged;
use Modules\Notification\Listeners\SendAppointmentNotification;

class NotificationServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Event::listen(
            AppointmentStatusChanged::class,
            SendAppointmentNotification::class
        );
    }
}
