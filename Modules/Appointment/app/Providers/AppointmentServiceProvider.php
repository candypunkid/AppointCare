<?php

namespace Modules\Appointment\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class AppointmentServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Appointment';
    protected string $nameLower = 'appointment';

    protected array $providers = [];
}
