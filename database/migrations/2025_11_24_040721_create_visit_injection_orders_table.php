<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_injection_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->onDelete('cascade');
            $table->foreignId('medicine_id')->constrained('medicines_master');
            $table->string('route', 10)->nullable();           // IM, IV, SC, ID
            $table->text('instruction')->nullable();           // "Stat", "In 100ml NS over 30min"
            $table->boolean('is_given')->default(false);
            $table->timestamp('given_at')->nullable();
            $table->foreignId('given_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['visit_id', 'medicine_id']); // no duplicate
            $table->index('is_given');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_injection_orders');
    }
};