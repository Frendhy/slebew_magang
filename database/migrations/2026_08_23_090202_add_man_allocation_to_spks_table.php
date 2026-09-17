<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spks', function (Blueprint $table) {
            $table->integer('man_allocation')->default(1)->after('due_date');
            $table->text('notes')->nullable()->after('man_allocation');
        });

        Schema::table('spk_materials', function (Blueprint $table) {
            $table->decimal('total_material_kg', 10, 2)->default(0)->after('total_batches_required');
        });
    }

    public function down(): void
    {
        Schema::table('spks', function (Blueprint $table) {
            foreach (['man_allocation', 'notes'] as $col) {
                if (Schema::hasColumn('spks', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
        Schema::table('spk_materials', function (Blueprint $table) {
            if (Schema::hasColumn('spk_materials', 'total_material_kg')) {
                $table->dropColumn('total_material_kg');
            }
        });
    }
};
