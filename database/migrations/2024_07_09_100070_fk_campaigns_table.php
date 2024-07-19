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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->foreign('brand_id', 'campaigns_brand_id')->references('brand_id')->on('brands')->onDelete('cascade')->onUpdate('cascade');
        });
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    { 
        Schema::table('campaigns', function (Blueprint $table) {
            
        });
    }
};
