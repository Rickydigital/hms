<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('pharmacy_sales', function (Blueprint $table) {
        $table->id();
        $table->string('invoice_no')->unique();
        $table->string('customer_name')->nullable();
        $table->string('customer_phone')->nullable();
        $table->decimal('total_amount', 12, 2);
        $table->decimal('amount_paid', 12, 2)->default(0);
        $table->decimal('change_due', 12, 2)->default(0);
        $table->text('remarks')->nullable();
        $table->foreignId('sold_by')->constrained('users');
        $table->timestamp('sold_at')->useCurrent();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_sales');
    }
};
