<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('store_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('store_items_master')->onDelete('cascade');
            $table->string('batch_no');
            $table->date('expiry_date');
            $table->integer('initial_quantity');
            $table->integer('current_stock');
            $table->decimal('purchase_price', 10, 2);
            $table->date('received_date')->useCurrent();
            $table->boolean('is_expired')->default(false);
            $table->timestamps();

            $table->unique(['item_id', 'batch_no']);
            $table->index(['expiry_date', 'is_expired']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_batches');
    }
};
