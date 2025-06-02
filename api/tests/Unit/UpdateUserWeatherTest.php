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
        Redis::flushall();
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

        // Run the job
        $job = new UpdateUserWeather($user);
        $job->handle();

        // Assert Redis has the data
        $cachedData = Redis::get('weather:' . $user->id);
        $this->assertNotNull($cachedData);

        $decodedData = json_decode($cachedData, true);
        $this->assertEquals($user->name, $decodedData['name']);
        $this->assertEquals($user->profile_picture, $decodedData['icon']);
        $this->assertNotNull($decodedData['weatherInfo']);
        $this->assertNotNull($decodedData['last_updated']);
    }

    public function test_job_handles_api_failure_gracefully()
    {
        // Create test user
        $user = User::factory()->create();

        // Mock failed API response
        Http::fake([
            'api.openweathermap.org/*' => Http::response([], 500),
        ]);

        // Run the job
        $job = new UpdateUserWeather($user);
        $job->handle();

        // Assert Redis doesn't have new data
        $cachedData = Redis::get('weather:' . $user->id);
        $this->assertNull($cachedData);
    }
} 
