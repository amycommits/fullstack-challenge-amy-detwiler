<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;
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

        // Store in Redis
        Redis::setex(
            'weather:' . $user->id,
            3600,
            json_encode($weatherData)
        );

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

        // Mock HTTP response
        Http::fake([
            'api.openweathermap.org/*' => Http::response(['error' => 'API Error'], 500),
        ]);

        // Fetch weather
        $result = $this->weatherService->fetchWeatherForUsers();

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($user->name, $result[0]['name']);
        $this->assertNull($result[0]['weatherInfo']);
        $this->assertEquals('Weather data temporarily unavailable', $result[0]['error']);
    }
} 