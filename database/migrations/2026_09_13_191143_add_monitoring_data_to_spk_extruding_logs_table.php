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
        Schema::table('spk_extruding_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('spk_extruding_logs', 'monitoring_data')) {
                $table->json('monitoring_data')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spk_extruding_logs', function (Blueprint $table) {
            if (Schema::hasColumn('spk_extruding_logs', 'monitoring_data')) {
                $table->dropColumn('monitoring_data');
            }
        });
    }
};
