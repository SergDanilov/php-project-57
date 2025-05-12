<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Новый'],
            ['name' => 'В работе'],
            ['name' => 'На тестировании'],
            ['name' => 'Завершен'],
        ];

        foreach ($statuses as $status) {
            Status::firstOrCreate(
                ['name' => $status['name']],
                $status
            );
        }
    }
}
