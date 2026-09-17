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
        Schema::table('spks', function (Blueprint $table) {
            if (!Schema::hasColumn('spks', 'customer')) {
                $table->string('customer')->nullable()->after('product_name');
            }
            if (!Schema::hasColumn('spks', 'ship_date')) {
                $table->date('ship_date')->nullable()->after('due_date');
            }
            if (!Schema::hasColumn('spks', 'target_op')) {
                $table->string('target_op')->nullable()->after('ship_date');
            }
            if (!Schema::hasColumn('spks', 'working_days')) {
                $table->string('working_days')->nullable()->after('target_op');
            }
            if (!Schema::hasColumn('spks', 'working_minutes')) {
                $table->string('working_minutes')->nullable()->after('working_days');
            }
            if (!Schema::hasColumn('spks', 'machine')) {
                $table->string('machine')->nullable()->after('working_minutes');
            }
            if (!Schema::hasColumn('spks', 'delay_hour')) {
                $table->string('delay_hour')->nullable()->after('machine');
            }
            if (!Schema::hasColumn('spks', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('spks', 'remarks')) {
                $table->text('remarks')->nullable()->after('keterangan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spks', function (Blueprint $table) {
            $columns = [
                'customer', 'ship_date', 'target_op', 'working_days',
                'working_minutes', 'machine', 'delay_hour', 'keterangan', 'remarks'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('spks', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
