<?php

namespace App\Providers;

use App\Events\AppointmentConfirmed;
use App\Events\AppointmentCancelled;
use App\Events\AppointmentRescheduled;
use App\Listeners\SendSMSNotification;
use App\Listeners\UpdateAnalytics;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AppointmentConfirmed::class => [
            [SendSMSNotification::class, 'handleConfirmed'],
            [UpdateAnalytics::class, 'handleConfirmed'],
        ],
        AppointmentCancelled::class => [
            [SendSMSNotification::class, 'handleCancelled'],
            [UpdateAnalytics::class, 'handleCancelled'],
        ],
        AppointmentRescheduled::class => [
            [SendSMSNotification::class, 'handleRescheduled'],
            [UpdateAnalytics::class, 'handleRescheduled'],
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
