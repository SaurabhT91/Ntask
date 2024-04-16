<?php

use App\Models\User;
use App\Models\Task;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create a project manager user
        User::factory()->create([
            'name' => 'Project manager',
            'email' => 'project@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create 10 regular users
        User::factory(10)->create();
    }
}


