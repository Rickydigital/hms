<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            // Step 1: Drop the foreign key constraint
            $table->dropForeign(['visit_id']);

            // Step 2: Drop the unique index
            $table->dropUnique(['visit_id']);

            // Step 3: Re-add the foreign key WITHOUT unique
            $table->foreign('visit_id')
                  ->references('id')
                  ->on('visits')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            // Rollback: drop foreign, add unique + foreign again
            $table->dropForeign(['visit_id']);

            $table->foreignId('visit_id')
                  ->unique()
                  ->constrained('visits')
                  ->onDelete('cascade');
        });
    }
};