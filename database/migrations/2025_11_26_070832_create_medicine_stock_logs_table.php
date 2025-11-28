<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicine_stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained('medicines_master')->onDelete('cascade');
            $table->foreignId('batch_id')->nullable()->constrained('medicine_batches')->onDelete('set null');
            $table->integer('quantity');           // +ve = IN, -ve = OUT
            $table->enum('type', ['purchase', 'sale', 'adjustment', 'return', 'damage', 'expiry']);
            $table->string('reference_type')->nullable();  // morph: sale, purchase, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->index(['medicine_id', 'created_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicine_stock_logs');
    }
};