<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Jobs\UpdateUserWeather;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateUserWeatherTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_job_updates_weather_data_successfully()
    {
        // Create test user
        $user = User::factory()->create();

        // Mock successful API response
        Http::fake([
            'api.openweathermap.org/*' => Http::response([
                'main' => ['temp' => 70],
                'weather' => [['description' => 'sunny']],
            ], 200),
        ]);

        // Mock Redis setex
        Redis::shouldReceive('setex')
            ->once()
            ->withArgs(function ($key, $ttl, $value) use ($user) {
                $data = json_decode($value, true);
                return $key === 'weather:' . $user->id
                    && $ttl === 3600
                    && $data['name'] === $user->name
                    && $data['icon'] === $user->profile_picture
                    && isset($data['weatherInfo'])
                    && isset($data['last_updated']);
            });

        // Run the job
        $job = new UpdateUserWeather($user);
        $job->handle();
    }

    public function test_job_handles_api_failure_gracefully()
    {
        // Create test user
        $user = User::factory()->create();

        // Mock failed API response
        Http::fake([
            'api.openweathermap.org/*' => Http::response([], 500),
        ]);

        // Mock Redis setex - should not be called
        Redis::shouldReceive('setex')
            ->never();

        // Run the job
        $job = new UpdateUserWeather($user);
        $job->handle();
    }
} 
