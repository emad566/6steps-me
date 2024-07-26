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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->bigIncrements('campaign_id');
            $table->string('campaign_no')->unique();
            $table->bigInteger('brand_id')->unsigned();
            $table->string('campaign_title');
            $table->text('campaign_description', 1000);
            $table->timestamp('start_at')->nullable();
            $table->timestamp('close_at')->nullable(); 
            $table->text('conditions', 1000);
            $table->string('product_image');
            $table->integer('ugc_no');
            $table->integer('ugc_videos_no');
            $table->integer('video_seconds_min');
            $table->integer('video_seconds_max');
            $table->integer('video_price');
            $table->integer('total_price')->virtualAs("ugc_no*ugc_videos_no*video_price");
            $table->boolean('is_usg_show')->default(1);
            $table->boolean('is_brand_show')->default(1);
            $table->boolean('is_tiktok');
            $table->boolean('is_instagram');
            $table->boolean('is_youtube');
            $table->boolean('is_sent_to_content_creator')->default(1);
            $table->enum('campaign_status', AppConstants::$campain_states)->default(AppConstants::$campain_states['0']);
            $table->text('reject_reason', 1000)->nullable();

 
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
