<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wards', function (Blueprint $table) {
            $table->id();
            $table->string('ward_code', 20)->unique();        // GEN01, ICU01, PRIV01
            $table->string('ward_name');                      // General Ward, ICU, Private Room
            $table->decimal('price_per_day', 10, 2);
            $table->integer('total_beds');
            $table->integer('available_beds')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('facilities')->nullable();           // AC, Oxygen, Monitor, etc.
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['ward_name', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wards');
    }
};