<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom day_number, shift_number, is_cadangan ke spk_assignments
     * dan batch_number, duration_seconds ke spk_extruding_logs
     */
    public function up(): void
    {
        Schema::table('spk_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('spk_assignments', 'day_number')) {
                $table->integer('day_number')->nullable()->after('shift');
            }
            if (!Schema::hasColumn('spk_assignments', 'shift_number')) {
                $table->integer('shift_number')->nullable()->after('day_number');
            }
            if (!Schema::hasColumn('spk_assignments', 'is_cadangan')) {
                $table->boolean('is_cadangan')->default(false)->after('shift_number');
            }
        });

        Schema::table('spk_extruding_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('spk_extruding_logs', 'batch_number')) {
                $table->integer('batch_number')->nullable()->after('worker_id');
            }
            if (!Schema::hasColumn('spk_extruding_logs', 'duration_seconds')) {
                $table->integer('duration_seconds')->nullable()->after('pressure');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spk_assignments', function (Blueprint $table) {
            foreach (['day_number', 'shift_number', 'is_cadangan'] as $col) {
                if (Schema::hasColumn('spk_assignments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('spk_extruding_logs', function (Blueprint $table) {
            foreach (['batch_number', 'duration_seconds'] as $col) {
                if (Schema::hasColumn('spk_extruding_logs', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
