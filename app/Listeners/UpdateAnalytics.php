<?php

namespace App\Listeners;

use App\Events\AppointmentCancelled;
use App\Events\AppointmentConfirmed;
use App\Events\AppointmentRescheduled;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateAnalytics
{
    public function handleConfirmed(AppointmentConfirmed $event): void
    {
        $this->incrementMetric('confirmed_count');
        $this->incrementMetric('ai_successful_actions');

        Log::info('Analytics updated: appointment confirmed', [
            'appointment_id' => $event->appointment->id,
        ]);
    }

    public function handleCancelled(AppointmentCancelled $event): void
    {
        $this->incrementMetric('cancelled_count');

        Log::info('Analytics updated: appointment cancelled', [
            'appointment_id' => $event->appointment->id,
        ]);
    }

    public function handleRescheduled(AppointmentRescheduled $event): void
    {
        $this->incrementMetric('rescheduled_count');

        Log::info('Analytics updated: appointment rescheduled', [
            'appointment_id' => $event->appointment->id,
        ]);
    }

    protected function incrementMetric(string $key): void
    {
        $analyticsKey = 'appointcare_analytics';

        $analytics = Cache::get($analyticsKey, [
            'confirmed_count' => 0,
            'cancelled_count' => 0,
            'rescheduled_count' => 0,
            'total_calls' => 0,
            'ai_successful_actions' => 0,
        ]);

        if (isset($analytics[$key])) {
            $analytics[$key]++;
        }

        Cache::put($analyticsKey, $analytics, now()->addDays(30));
    }
}
