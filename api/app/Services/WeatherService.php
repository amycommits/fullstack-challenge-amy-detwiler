<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('OPENWEATHER_API_KEY');
        Log::info('WeatherService initialized with API key: ' . substr($this->apiKey, 0, 4) . '...');
    }

    public function fetchWeatherForUsers()
    {
        $users = User::all();
        Log::info('Found ' . $users->count() . ' users');
        $weatherData = [];

        foreach ($users as $user) {
            $cacheKey = "weather_{$user->id}";
            Log::info("Processing user {$user->id} with cache key: {$cacheKey}");

            // Check if the weather data is cached
            if (Cache::has($cacheKey)) {
                Log::info("Cache hit for user {$user->id}");
                $weatherData[] = Cache::get($cacheKey);
                continue;
            }

            Log::info("Cache miss for user {$user->id}, fetching from API");
            // Fetch weather data from OpenWeatherMap API
            $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
                'lat' => $user->latitude,
                'lon' => $user->longitude,
                'appid' => $this->apiKey,
                'units' => 'imperial',
            ]);

            if ($response->successful()) {
                $weatherInfo = $response->json();
                $userWeatherInfo = [
                    'name' => $user->name,
                    'icon' => $user->profile_picture,
                    'weatherInfo' => $weatherInfo,
                ];

                // Cache the weather data for 45 minutes
                Cache::put($cacheKey, $userWeatherInfo, 45 * 60);
                Log::info("Successfully cached weather data for user {$user->id}");
                $weatherData[] = $userWeatherInfo;
            } else {
                Log::error("Failed to fetch weather for user {$user->id}: " . $response->body());
            }
        }

        return $weatherData;
    }
}
