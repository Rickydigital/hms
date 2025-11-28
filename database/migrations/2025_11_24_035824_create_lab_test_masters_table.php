<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_tests_master', function (Blueprint $table) {
            $table->id();
            $table->string('test_code', 20)->unique();     
            $table->string('test_name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['test_name', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_tests_master');
    }
};