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
        Schema::table('visit_medicine_orders', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false)->after('is_issued');
            $table->timestamp('paid_at')->nullable()->after('is_paid');
            $table->unsignedBigInteger('paid_by')->nullable()->after('paid_at');
            $table->foreign('paid_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visit_medicine_orders', function (Blueprint $table) {
            //
        });
    }
};
