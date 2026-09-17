<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spk_bagging_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spk_id')->constrained('spks')->cascadeOnDelete();
            $table->foreignId('worker_id')->constrained('users');
            $table->integer('sak_count');
            $table->decimal('weight_kg', 8, 2);
            $table->integer('duration_minutes');
            $table->integer('reject_count')->default(0);
            $table->string('qc_status')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spk_bagging_logs');
    }
};
