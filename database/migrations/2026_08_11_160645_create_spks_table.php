<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spks', function (Blueprint $table) {
            $table->id();
            $table->string('spk_number')->unique();
            $table->string('product_name');
            $table->string('status')->default('upcoming'); // upcoming, ongoing, done
            $table->string('current_step')->nullable(); // transfer, weighing, mixing, extruding, cleaning
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spks');
    }
};
