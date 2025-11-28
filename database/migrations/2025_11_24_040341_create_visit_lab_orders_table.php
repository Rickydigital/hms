<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_lab_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->onDelete('cascade');
            $table->foreignId('lab_test_id')->constrained('lab_tests_master');
            $table->text('extra_instruction')->nullable();     // "Fasting", "Urgent", etc.
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['visit_id', 'lab_test_id']); // no duplicate test in one visit
            $table->index('is_completed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_lab_orders');
    }
};