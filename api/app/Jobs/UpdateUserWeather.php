<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class UpdateUserWeather implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected const REQUEST_TIMEOUT = 0.5; // 500ms timeout
    protected const REDIS_PREFIX = 'weather:';

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $response = Http::timeout(self::REQUEST_TIMEOUT)
                ->get("https://api.openweathermap.org/data/2.5/weather", [
                    'lat' => $this->user->latitude,
                    'lon' => $this->user->longitude,
                    'appid' => env('OPENWEATHER_API_KEY'),
                    'units' => 'imperial',
                ]);

            if ($response->successful()) {
                $weatherInfo = $response->json();
                $userWeatherInfo = [
                    'name' => $this->user->name,
                    'icon' => $this->user->profile_picture,
                    'weatherInfo' => $weatherInfo,
                    'last_updated' => now()->timestamp,
                ];

                Redis::setex(
                    self::REDIS_PREFIX . $this->user->id,
                    3600, // 1 hour TTL
                    json_encode($userWeatherInfo)
                );
            } else {
                Log::error("Failed to fetch weather for user {$this->user->id}: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Error updating weather for user {$this->user->id}: " . $e->getMessage());
        }
    }
} 