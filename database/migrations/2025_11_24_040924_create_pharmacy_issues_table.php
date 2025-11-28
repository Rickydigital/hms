<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pharmacy_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_medicine_order_id')
                  ->unique()
                  ->constrained('visit_medicine_orders')
                  ->onDelete('cascade');
            $table->foreignId('medicine_id')->constrained('medicines_master');
            $table->string('batch_no');
            $table->date('expiry_date');
            $table->integer('quantity_issued');
            $table->decimal('unit_price', 8, 2);
            $table->decimal('total_amount', 10, 2);
            $table->foreignId('issued_by')->constrained('users');
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamps();

            $table->index(['medicine_id', 'batch_no']);
            $table->index('issued_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pharmacy_issues');
    }
};