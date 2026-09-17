<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('weekly_schedules', function (Blueprint $table) {
            $table->id();
            $table->date('start_date')->unique(); // Monday of the week
            $table->date('end_date'); // Sunday of the week
            $table->string('morning_shift_team')->nullable(); // e.g., yellow
            $table->string('afternoon_shift_team')->nullable(); // e.g., red
            $table->string('night_shift_team')->nullable(); // e.g., green
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_schedules');
    }
};
