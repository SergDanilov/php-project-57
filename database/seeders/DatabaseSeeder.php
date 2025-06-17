<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // User::factory(10)->create();

        $this->call([
            TaskStatusSeeder::class,
            LabelSeeder::class,
            // Другие сидеры...
        ]);
    }
}
