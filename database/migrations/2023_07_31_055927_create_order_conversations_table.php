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
        Schema::create('order_conversations', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('order_number')->unique();
            $table->foreignId('owner');
            $table->foreignId(' customer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_conversations');
    }
};
