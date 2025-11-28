<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicine_stock_logs', function (Blueprint $table) {
            $table->integer('stock_before')->default(0)->after('quantity');
            $table->integer('stock_after')->default(0)->after('stock_before');
        });
    }

    public function down(): void
    {
        Schema::table('medicine_stock_logs', function (Blueprint $table) {
            $table->dropColumn(['stock_before', 'stock_after']);
        });
    }
};