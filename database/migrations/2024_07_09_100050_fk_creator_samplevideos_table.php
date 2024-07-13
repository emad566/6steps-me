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
        Schema::table('creator_samplevideos', function (Blueprint $table) {
            $table->foreign('creator_id')->references('creator_id')->on('creators')->onDelete('cascade')->onUpdate('cascade');
        });
    }
};
