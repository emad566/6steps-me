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
        Schema::create('brands', function (Blueprint $table) {
            $table->bigIncrements('brand_id');
            $table->string('brand_name')->nullable();
            $table->string('mobile')->unique()->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('otp')->nullable();
            $table->timestamp('otp_created_at')->nullable();
            $table->string('password')->nullable();
            $table->string('logo')->nullable();
            $table->string('website_url')->nullable();
            $table->text('description', 500)->nullable();
            $table->string('address')->nullable();
            $table->string('branches_no')->nullable();
            $table->string('tax_no')->nullable();
            $table->string('cr_no')->nullable();
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
        Schema::dropIfExists('brands');
    }
};
