<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('statuses', 'task_statuses');
    }

    public function down(): void
    {
        Schema::rename('task_statuses', 'statuses');
    }
};
