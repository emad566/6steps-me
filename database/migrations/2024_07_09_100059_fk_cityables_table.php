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
        Schema::table('cityables', function (Blueprint $table) {
            $table->foreign('city_city_id')->references('city_id')->on('cities')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cityables', function (Blueprint $table) {
            $table->dropForeign(['city_city_id_foreign']);
        });
    }
};
