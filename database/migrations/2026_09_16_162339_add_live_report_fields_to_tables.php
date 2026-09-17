<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spk_cleaning_logs', function (Blueprint $table) {
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('qc_status')->nullable();
            $table->text('qc_note')->nullable();
            $table->string('qc_by')->nullable();
            $table->timestamp('qc_at')->nullable();
            $table->json('material_pendukung')->nullable();
            $table->json('material_hasil')->nullable();
        });

        Schema::table('spk_batches', function (Blueprint $table) {
            $table->decimal('temp_hot', 5, 2)->nullable();
            $table->decimal('temp_cold', 5, 2)->nullable();
        });

        Schema::table('spk_extruding_logs', function (Blueprint $table) {
            $table->decimal('temp_zone1', 5, 2)->nullable();
            $table->decimal('temp_zone2', 5, 2)->nullable();
            $table->decimal('temp_zone3', 5, 2)->nullable();
            $table->decimal('temp_zone4', 5, 2)->nullable();
            $table->integer('rpm')->nullable();
            $table->decimal('ampere', 5, 2)->nullable();
            $table->string('qc_status')->nullable();
            $table->text('qc_note')->nullable();
        });

        Schema::table('spk_transfer_logs', function (Blueprint $table) {
            $table->string('slip_number')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->nullable();
            $table->string('received_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('spk_cleaning_logs', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time', 'qc_status', 'qc_note', 'qc_by', 'qc_at', 'material_pendukung', 'material_hasil']);
        });

        Schema::table('spk_batches', function (Blueprint $table) {
            $table->dropColumn(['temp_hot', 'temp_cold']);
        });

        Schema::table('spk_extruding_logs', function (Blueprint $table) {
            $table->dropColumn(['temp_zone1', 'temp_zone2', 'temp_zone3', 'temp_zone4', 'rpm', 'ampere', 'qc_status', 'qc_note']);
        });

        Schema::table('spk_transfer_logs', function (Blueprint $table) {
            $table->dropColumn(['slip_number', 'location', 'status', 'received_by']);
        });
    }
};
