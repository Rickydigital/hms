<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicine_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->date('invoice_date');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->decimal('total_amount', 12, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('net_amount', 12, 2);
            $table->text('remarks')->nullable();
            $table->foreignId('received_by')->constrained('users');
            $table->timestamp('received_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicine_purchases');
    }
};