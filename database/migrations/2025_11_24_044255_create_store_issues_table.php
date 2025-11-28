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
        Schema::create('store_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_batch_id')->constrained('store_batches')->onDelete('cascade');
            $table->integer('quantity_issued');
            $table->foreignId('issued_to')->constrained('users');     // Lab tech or Pharmacist
            $table->foreignId('requested_by')->nullable()->constrained('users');
            $table->text('purpose')->nullable();                      // For CBC test, Injection, etc.
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_issues');
    }
};
