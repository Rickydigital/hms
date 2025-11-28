<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_bed_admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->unique()->constrained('visits')->onDelete('cascade');
            $table->foreignId('ward_id')->constrained('wards');
            $table->date('admission_date')->useCurrent();
            $table->date('discharge_date')->nullable();
            $table->integer('total_days')->default(0);
            $table->decimal('bed_charges', 10, 2)->default(0);
            $table->text('admission_reason')->nullable();
            $table->text('doctor_instruction')->nullable();
            $table->boolean('is_discharged')->default(false);
            $table->timestamp('discharged_at')->nullable();
            $table->foreignId('discharged_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['admission_date', 'is_discharged']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_bed_admissions');
    }
};