<?php

return [
    'api_key' => env('OPENWEATHER_API_KEY'),
    'base_url' => 'https://api.openweathermap.org/data/2.5/weather',
    'cache_ttl' => 3600, // Cache time-to-live in seconds
    'units' => env('OPENWEATHER_UNIT', 'imperial'),
];
