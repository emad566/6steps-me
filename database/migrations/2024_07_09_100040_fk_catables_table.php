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
        Schema::table('catables', function (Blueprint $table) {
            $table->foreign('cat_cat_id', 'cat_cat_id')->references('cat_id')->on('cats')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('catables', function (Blueprint $table) {
            $table->dropForeign(['cat_cat_id']);
        });
    }
};
