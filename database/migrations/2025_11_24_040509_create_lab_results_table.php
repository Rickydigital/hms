<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_lab_order_id')->unique()->constrained('visit_lab_orders')->onDelete('cascade');
            $table->text('result_value')->nullable();        // e.g., 12.5
            $table->text('result_text')->nullable();         // e.g., "Normal", "Widal Positive"
            $table->text('remarks')->nullable();
            $table->string('normal_range')->nullable();      // e.g., 11-15 g/dL
            $table->boolean('is_abnormal')->default(false);
            $table->foreignId('technician_id')->nullable()->constrained('users');
            $table->timestamp('reported_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_results');
    }
};