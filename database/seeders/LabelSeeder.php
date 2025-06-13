<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Label;
use Illuminate\Database\Seeder;

class LabelSeeder extends Seeder
{
    public function run(): void
    {
        $labels = [
            ['name' => 'новая'],
            ['name' => 'в работе'],
            ['name' => 'тестирование'],
            ['name' => 'в архиве'],
            ['name' => 'срочно!'],
        ];

        foreach ($labels as $label) {
            Label::firstOrCreate(
                ['name' => $label['name']],
                $label
            );
        }
    }
}
