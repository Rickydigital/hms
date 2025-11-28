<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->unique()->constrained('visits')->onDelete('cascade');
            $table->string('receipt_no', 20)->unique();           // RCPT2025-000001
            $table->decimal('total_registration', 10, 2)->default(0);
            $table->decimal('total_final', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);
            $table->string('pdf_path')->nullable();               // storage/receipts/xxx.pdf
            $table->timestamp('generated_at')->useCurrent();
            $table->foreignId('generated_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};