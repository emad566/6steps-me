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
        Schema::create('campaign_requests', function (Blueprint $table) {
            $table->bigIncrements('request_id');
            $table->string('request_no')->unique();
            $table->bigInteger('campaign_id')->unsigned();
            $table->bigInteger('brand_id')->unsigned();
            $table->bigInteger('creator_id')->unsigned();
            $table->text('explanation', 1000);
            
            $table->enum('campaign_status', AppConstants::$request_states)->default(AppConstants::$request_states['0']);
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
        Schema::dropIfExists('campaign_requests');
    }
};
