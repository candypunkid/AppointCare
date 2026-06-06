<?php

return [
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'realtime_model' => env('OPENAI_REALTIME_MODEL', 'gpt-4o-realtime-preview'),
        'websocket_url' => 'wss://api.openai.com/v1/realtime',
        'input_audio_format' => 'g711_ulaw',
        'output_audio_format' => 'g711_ulaw',
        'sample_rate' => 8000,
    ],
];
