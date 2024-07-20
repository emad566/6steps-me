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
        Schema::table('request_videos', function (Blueprint $table) {
            $table->foreign('request_id', 'request_videos_request_id')->references('request_id')->on('campaign_requests')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('campaign_id', 'request_videos_campaign_id')->references('campaign_id')->on('campaigns')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('brand_id', 'request_videos_brand_id')->references('brand_id')->on('brands')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('creator_id', 'request_videos_creator_id')->references('creator_id')->on('creators')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    { 
        Schema::table('request_videos', function (Blueprint $table) {
            
        });
    }
};
