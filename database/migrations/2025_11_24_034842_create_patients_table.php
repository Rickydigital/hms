<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id', 15)->unique();           
            $table->string('name');
            $table->integer('age');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('phone', 15)->nullable();
            $table->text('address')->nullable();
            $table->date('registration_date')->useCurrent();
            $table->date('expiry_date');                          
            $table->boolean('is_active')->default(true);   
            $table->decimal('reactivation_fee_paid', 8, 2)->default(0);
            $table->integer('total_visits')->default(0);
            $table->timestamps();

            
            $table->index('patient_id');
            $table->index('phone');
            $table->index('name');
            $table->index('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};