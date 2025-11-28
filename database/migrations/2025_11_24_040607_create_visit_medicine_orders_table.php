<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_medicine_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->onDelete('cascade');
            $table->foreignId('medicine_id')->constrained('medicines_master');
            $table->string('dosage');                    // 1-0-1, 1-1-1, etc.
            $table->integer('duration_days');
            $table->text('instruction')->nullable();     // After food, SOS, etc.
            $table->boolean('is_issued')->default(false);
            $table->timestamp('issued_at')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['visit_id', 'medicine_id']); // no duplicate medicine
            $table->index('is_issued');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_medicine_orders');
    }
};