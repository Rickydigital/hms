<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('restrict');
            $table->date('visit_date')->useCurrent();
            $table->time('visit_time')->useCurrent();
            $table->string('status', 20)->default('Waiting'); 
            $table->decimal('registration_amount', 8, 2)->default(0);
            $table->boolean('registration_paid')->default(false);
            $table->boolean('all_services_completed')->default(false);
            $table->timestamps();

            $table->index(['visit_date', 'status']);
            $table->index('doctor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};