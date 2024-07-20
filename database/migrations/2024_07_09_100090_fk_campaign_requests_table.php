<?php

use App\Models\AppConstants;
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
        Schema::table('campaign_requests', function (Blueprint $table) {
            $table->foreign('campaign_id', 'campaign_requests_campaign_id')->references('campaign_id')->on('campaigns')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('brand_id', 'campaign_requests_brand_id')->references('brand_id')->on('brands')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('creator_id', 'campaign_requests_creator_id')->references('creator_id')->on('creators')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaign_requests', function (Blueprint $table) {
            
        });
    }
};
