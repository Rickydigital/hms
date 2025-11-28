<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicine_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained('medicines_master')->onDelete('cascade');
            $table->foreignId('purchase_id')->nullable()->constrained('medicine_purchases')->onDelete('set null');
            $table->string('batch_no');
            $table->date('expiry_date');
            $table->integer('initial_quantity');
            $table->integer('current_stock');
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->date('manufacturing_date')->nullable();
            $table->date('received_date')->useCurrent();
            $table->boolean('is_expired')->default(false);
            $table->timestamps();

            $table->unique(['medicine_id', 'batch_no']);
            $table->index(['expiry_date', 'is_expired']);
            $table->index('current_stock');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicine_batches');
    }
};