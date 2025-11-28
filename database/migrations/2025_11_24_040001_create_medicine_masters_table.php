<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicines_master', function (Blueprint $table) {
            $table->id();
            $table->string('medicine_code', 20)->unique();        // MED001
            $table->string('medicine_name');
            $table->string('generic_name')->nullable();
            $table->string('packing');                            // 10x10, 100ml, etc.
            $table->string('type')->default('Tablet');           // Tablet, Capsule, Syrup, Injection, Ointment
            $table->decimal('price', 8, 2);
            $table->decimal('purchase_price', 8, 2)->nullable();
            $table->boolean('is_injectable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('minimum_stock')->default(10);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['medicine_name', 'is_active']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicines_master');
    }
};