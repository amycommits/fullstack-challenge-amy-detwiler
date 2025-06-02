<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;

class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function getUsersWeather(): JsonResponse
    {
        $usersWeather = $this->weatherService->fetchWeatherForUsers();
        return response()->json($usersWeather);
    }
}
