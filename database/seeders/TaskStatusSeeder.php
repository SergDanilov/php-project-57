<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\TaskStatus;
use Illuminate\Database\Seeder;

class TaskStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Новый'],
            ['name' => 'В работе'],
            ['name' => 'На тестировании'],
            ['name' => 'Завершен'],
        ];

        foreach ($statuses as $status) {
            TaskStatus::firstOrCreate(
                ['name' => $status['name']],
                $status
            );
        }
    }
}
