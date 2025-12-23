<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patient_vitals', function (Blueprint $table) {
            $table->text('examination')->nullable()->after('history');
            $table->text('diagnosis')->nullable()->after('examination');
        });
    }

    public function down(): void
    {
        Schema::table('patient_vitals', function (Blueprint $table) {
            $table->dropColumn(['examination', 'diagnosis']);
        });
    }
};