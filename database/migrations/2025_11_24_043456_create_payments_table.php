<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['Registration', 'Final']);  // Only two types
            $table->string('payment_method')->default('Cash'); // Cash, UPI, Card, Bank Transfer
            $table->text('transaction_id')->nullable();        // For UPI/Card reference
            $table->foreignId('received_by')->constrained('users');
            $table->timestamp('paid_at')->useCurrent();
            $table->timestamps();

            $table->index(['visit_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};