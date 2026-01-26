<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('visit_medicine_orders', function (Blueprint $table) {
            // 1) ensure there is a normal index for foreign key needs
            $table->index(['visit_id', 'medicine_id'], 'vmo_visit_id_medicine_id_index');

            // 2) now drop the unique index
            $table->dropUnique('visit_medicine_orders_visit_id_medicine_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('visit_medicine_orders', function (Blueprint $table) {
            // restore unique
            $table->unique(['visit_id', 'medicine_id'], 'visit_medicine_orders_visit_id_medicine_id_unique');

            // remove the normal index (optional)
            $table->dropIndex('vmo_visit_id_medicine_id_index');
        });
    }
};