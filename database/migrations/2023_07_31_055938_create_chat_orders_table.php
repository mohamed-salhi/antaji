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
        Schema::create('chat_orders', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('msg');
            $table->foreignId('user_uuid');
            $table->foreignUuid('order_conversation_uuid')->references('uuid')->on('order_conversations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_orders');
    }
};
