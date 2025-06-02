<?php

namespace App\Services;

use App\Models\User;
use App\Jobs\UpdateUserWeather;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class WeatherService
{
    protected $apiKey;
    protected const CACHE_TTL = 3600; // 1 hour in seconds
    protected const REQUEST_TIMEOUT = 0.5; // 500ms timeout
    protected const REDIS_PREFIX = 'weather:';

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
            $cacheKey = self::REDIS_PREFIX . $user->id;
            Log::info("Processing user {$user->id} with cache key: {$cacheKey}");
            
            try {
                // Check Redis for cached data
                $cachedData = Redis::get($cacheKey);
                if ($cachedData) {
                    $cachedData = json_decode($cachedData, true);
                    if ($this->isDataFresh($cachedData)) {
                        $weatherData[] = $cachedData;
                        continue;
                    }
                }

                // If no fresh data, dispatch job to update weather
                UpdateUserWeather::dispatch($user)->onQueue('weather');

                // Return cached data if available, even if stale
                if ($cachedData) {
                    $weatherData[] = $cachedData;
                } else {
                    $weatherData[] = [
                        'name' => $user->name,
                        'icon' => $user->profile_picture,
                        'weatherInfo' => null,
                        'error' => 'Weather data temporarily unavailable',
                        'last_updated' => now()->timestamp,
                    ];
                }
            } catch (\Exception $e) {
                Log::error("Error processing weather for user {$user->id}: " . $e->getMessage());
                $weatherData[] = [
                    'name' => $user->name,
                    'icon' => $user->profile_picture,
                    'weatherInfo' => null,
                    'error' => 'Weather data temporarily unavailable',
                    'last_updated' => now()->timestamp,
                ];
            }
        }

        return $weatherData;
    }

    protected function isDataFresh($data): bool
    {
        if (!isset($data['last_updated'])) {
            return false;
        }

        $lastUpdated = $data['last_updated'];
        $oneHourAgo = now()->subHour()->timestamp;
        
        return $lastUpdated >= $oneHourAgo;
    }
}
