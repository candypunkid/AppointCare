<?php

namespace Modules\Twilio\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Twilio\Services\TwilioService;

class TwilioServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TwilioService::class, function ($app) {
            return new TwilioService;
        });
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
    }
}
