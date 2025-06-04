<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WeatherService;

class UpdateUsersWeather extends Command
{
    protected $signature = 'weather:update-users';
    protected $description = 'Update weather information for all users';

    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        parent::__construct();
        $this->weatherService = $weatherService;
    }

    public function handle()
    {
        $this->weatherService->fetchWeatherForUsers();
        $this->info('Weather information for all users has been updated successfully.');
    }
}
