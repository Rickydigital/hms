<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_vitals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->unique()->constrained('visits')->onDelete('cascade');
            $table->decimal('height', 5, 2)->nullable();        
            $table->decimal('weight', 5, 2)->nullable();        
            $table->string('bp', 10)->nullable();               
            $table->decimal('temperature', 4, 1)->nullable();   
            $table->integer('pulse')->nullable();
            $table->integer('respiration')->nullable();
            $table->text('chief_complaint')->nullable();
            $table->text('history')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_vitals');
    }
};