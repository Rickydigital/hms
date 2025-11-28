<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('type', 50)->nullable()->change(); // or remove enum completely
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('type', ['advance', 'final', 'refund'])->change();
        });
    }
};