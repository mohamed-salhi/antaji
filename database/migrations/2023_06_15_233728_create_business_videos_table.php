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
        Schema::create('business_videos', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->integer('view')->default(0);
            $table->string('title')->nullable();
            $table->string('status')->default(1);

            $table->string('time')->nullable();
            $table->foreignUuid('user_uuid')->references('uuid')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_videos');
    }
};
