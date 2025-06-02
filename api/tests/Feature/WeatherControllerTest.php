<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WeatherControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Redis::flushall();
    }

    public function test_get_users_weather_returns_correct_data()
    {
        // Create test users
        $users = User::factory()->count(3)->create();

        // Mock weather data in Redis
        foreach ($users as $user) {
            $weatherData = [
                'name' => $user->name,
                'icon' => $user->profile_picture,
                'weatherInfo' => [
                    'main' => ['temp' => 70],
                    'weather' => [['description' => 'sunny', 'icon' => '01d']],
                ],
                'last_updated' => now()->timestamp,
            ];

            Redis::setex(
                'weather:' . $user->id,
                3600,
                json_encode($weatherData)
            );
        }

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
        // Create test users but don't add weather data
        User::factory()->count(3)->create();

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
