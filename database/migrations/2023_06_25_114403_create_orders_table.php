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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('order_number');
            $table->foreignUuid('delivery_addresses_uuid')->nullable()->references('uuid')->on('users')->nullOnDelete();
            $table->string('type')->nullable();//اجار ||بيع
            $table->date('start')->nullable();
            $table->date('end')->nullable();
            $table->double('commission');
            $table->double('delivery')->nullable();
            $table->double('multi_day_discounts')->nullable();
            $table->double('price_with_day')->nullable();
            $table->string('status')->default('inactive');
            $table->foreignUuid('user_uuid')->references('uuid')->on('users')->cascadeOnDelete();
            $table->foreignUuid('content_uuid');
            $table->string('content_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
