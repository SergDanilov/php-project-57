<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::table('tasks', function (Blueprint $table) {
        //     $table->foreignId('status_id')->constrained('statuses');
        // });
    }

    public function down(): void
    {
        // Schema::table('tasks', function (Blueprint $table) {
        //     //
        // });
    }
};
