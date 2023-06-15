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
        Schema::create('busines', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->integer('view')->default(0);
            $table->enum('type',['video','images']);
            $table->string('title')->nullable();
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
        Schema::dropIfExists('busines');
    }
};
