<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Services\WeatherService;
use App\Jobs\UpdateUserWeather;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WeatherServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $weatherService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->weatherService = new WeatherService();
    }

    public function test_fetch_weather_for_users_returns_cached_data_when_fresh()
    {
        // Create test user
        $user = User::factory()->create();

        // Mock weather data
        $weatherData = [
            'name' => $user->name,
            'icon' => $user->profile_picture,
            'weatherInfo' => [
                'main' => ['temp' => 70],
                'weather' => [['description' => 'sunny']],
            ],
            'last_updated' => now()->timestamp,
        ];

        // Mock Redis to return fresh data
        Redis::shouldReceive('get')
            ->with('weather:' . $user->id)
            ->andReturn(json_encode($weatherData));

        // Fetch weather
        $result = $this->weatherService->fetchWeatherForUsers();

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($user->name, $result[0]['name']);
        $this->assertEquals($user->profile_picture, $result[0]['icon']);
    }

    public function test_fetch_weather_for_users_returns_error_when_no_cache()
    {
        // Create test user
        $user = User::factory()->create();

        // Mock Redis to return null (no cache)
        Redis::shouldReceive('get')
            ->with('weather:' . $user->id)
            ->andReturn(null);

        // Mock Queue to verify job dispatch
        Queue::fake();

        // Fetch weather
        $result = $this->weatherService->fetchWeatherForUsers();

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($user->name, $result[0]['name']);
        $this->assertNull($result[0]['weatherInfo']);
        $this->assertEquals('Weather data temporarily unavailable', $result[0]['error']);

        // Assert job was dispatched
        Queue::assertPushed(UpdateUserWeather::class);
    }

    public function test_fetch_weather_for_users_handles_stale_cache()
    {
        // Create test user
        $user = User::factory()->create();

        // Mock weather data with stale timestamp (more than 1 hour old)
        $weatherData = [
            'name' => $user->name,
            'icon' => $user->profile_picture,
            'weatherInfo' => [
                'main' => ['temp' => 70],
                'weather' => [['description' => 'sunny']],
            ],
            'last_updated' => now()->subHours(2)->timestamp, // 2 hours old
        ];

        // Mock Redis to return stale data
        Redis::shouldReceive('get')
            ->with('weather:' . $user->id)
            ->andReturn(json_encode($weatherData));

        // Mock Queue to verify job dispatch
        Queue::fake();

        // Fetch weather
        $result = $this->weatherService->fetchWeatherForUsers();

        // Assert stale data is returned
        $this->assertCount(1, $result);
        $this->assertEquals($user->name, $result[0]['name']);
        $this->assertEquals($user->profile_picture, $result[0]['icon']);

        // Assert job was dispatched to update stale data
        Queue::assertPushed(UpdateUserWeather::class);
    }
} 