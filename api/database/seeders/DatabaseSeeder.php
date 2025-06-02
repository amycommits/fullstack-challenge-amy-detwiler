<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // Create test users with coordinates
        User::create([
            'name' => 'Test User 1',
            'email' => 'test1@example.com',
            'password' => Hash::make('password'),
            'latitude' => 40.7128,  // New York City
            'longitude' => -74.0060
        ]);

        User::create([
            'name' => 'Test User 2',
            'email' => 'test2@example.com',
            'password' => Hash::make('password'),
            'latitude' => 51.5074,  // London
            'longitude' => -0.1278
        ]);

        User::create([
            'name' => 'Test User 3',
            'email' => 'test3@example.com',
            'password' => Hash::make('password'),
            'latitude' => 35.6762,  // Tokyo
            'longitude' => 139.6503
        ]);
    }
}
