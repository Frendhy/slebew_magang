<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spk_machine_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spk_id')->constrained('spks')->cascadeOnDelete();
            $table->decimal('temp_zone1', 6, 2)->comment('Suhu Zona 1 (°C)');
            $table->decimal('temp_zone2', 6, 2)->comment('Suhu Zona 2 (°C)');
            $table->decimal('temp_zone3', 6, 2)->comment('Suhu Zona 3 (°C)');
            $table->decimal('pressure_bar', 5, 2)->comment('Tekanan (Bar)');
            $table->integer('rpm_speed')->comment('Kecepatan RPM');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spk_machine_settings');
    }
};
