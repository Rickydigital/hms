<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->unsignedTinyInteger('age_months')->nullable()->after('age');
            $table->unsignedTinyInteger('age_days')->nullable()->after('age_months');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['age_months', 'age_days']);
        });
    }
};