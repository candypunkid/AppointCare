<?php

return [
    'admin_phone' => env('APPOINTCARE_ADMIN_PHONE', ''),

    'business_hours_start' => env('BUSINESS_HOURS_START', 9),
    'business_hours_end' => env('BUSINESS_HOURS_END', 17),
    'slot_duration_minutes' => env('SLOT_DURATION_MINUTES', 60),
    'max_slots_per_slot' => env('MAX_SLOTS_PER_SLOT', 1),

    'reminder' => [
        '24h_enabled' => env('REMINDER_24H_ENABLED', true),
        '2h_enabled' => env('REMINDER_2H_ENABLED', true),
    ],
];
