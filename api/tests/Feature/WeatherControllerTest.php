<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Jobs\UpdateUserWeather;

class WeatherControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_get_users_weather_returns_correct_data()
    {
        // Create test users
        $users = User::factory()->count(3)->create();

        // Mock the WeatherService
        $this->mock(WeatherService::class, function ($mock) use ($users) {
            $mock->shouldReceive('fetchWeatherForUsers')
                ->once()
                ->andReturn($users->map(function ($user) {
                    return [
                        'name' => $user->name,
                        'icon' => $user->profile_picture,
                        'weatherInfo' => [
                            'main' => ['temp' => 70],
                            'weather' => [['description' => 'sunny', 'icon' => '01d']],
                        ],
                        'last_updated' => now()->timestamp,
                    ];
                })->toArray());
        });

        // Make request
        $response = $this->getJson('/api/users/weather');

        // Assert response
        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => [
                    'name',
                    'icon',
                    'weatherInfo' => [
                        'main' => ['temp'],
                        'weather' => [['description', 'icon']],
                    ],
                    'last_updated',
                ],
            ]);
    }

    public function test_get_users_weather_handles_no_data()
    {
        // Create test users
        $users = User::factory()->count(3)->create();

        // Mock the WeatherService to return error data
        $this->mock(WeatherService::class, function ($mock) use ($users) {
            $mock->shouldReceive('fetchWeatherForUsers')
                ->once()
                ->andReturn($users->map(function ($user) {
                    return [
                        'name' => $user->name,
                        'icon' => $user->profile_picture,
                        'error' => 'No weather data available',
                        'last_updated' => now()->timestamp,
                    ];
                })->toArray());
        });

        // Make request
        $response = $this->getJson('/api/users/weather');

        // Assert response
        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => [
                    'name',
                    'icon',
                    'error',
                    'last_updated',
                ],
            ]);
    }
} 
