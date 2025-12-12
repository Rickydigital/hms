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
            $table->timestamp('handed_over_at')->nullable()->after('paid_at');
            $table->unsignedBigInteger('handed_over_by')->nullable()->after('handed_over_at');
            $table->foreign('handed_over_by')->references('id')->on('users');
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
