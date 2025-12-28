<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin
        Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Create User
        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
        ]);

        // Create Sample Events
        Event::create(['name' => 'Laravel Conference 2025', 'capacity' => 500]);
        Event::create(['name' => 'PHP Meetup', 'capacity' => 100]);
        Event::create(['name' => 'Docker Workshop', 'capacity' => 50]);
        Event::create(['name' => 'Redis Masterclass', 'capacity' => 200]);
        Event::create(['name' => 'API Design Summit', 'capacity' => 300]);
    }
}
