<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        // User::factory(3)->create();
        // Team::factory(2)->create();
        // Task::factory(5)->create();
        Plan::create([
            'name' => 'free',
            'max_created_teams' => 1,
            'max_joined_teams' => 1,
            'price' => 0
        ]);

        Plan::create([
            'name' => 'pro',
            'max_created_teams' => 300,
            'max_joined_teams' => 1000,
            'price' => 29.99
        ]);
    }
}
