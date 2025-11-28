<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('store_items_master', function (Blueprint $table) {
            $table->id();
            $table->string('item_code', 20)->unique();     // STR001
            $table->string('item_name');
            $table->string('unit');                         // Piece, Pair, Box, Roll
            $table->decimal('price', 10, 2);
            $table->integer('minimum_stock')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('store_items_master'); }
};