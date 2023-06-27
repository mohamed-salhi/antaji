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
        Schema::create('order_products', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('order_number');
            $table->string('type');//اجار ||بيع
            $table->date('start')->nullable();
            $table->date('end')->nullable();
            $table->double('commission');
            $table->double('multi_day_discounts');
            $table->double('price_with_day');
            $table->string('status')->default('pending');
            $table->foreignUuid('user_uuid')->references('uuid')->on('users')->cascadeOnDelete();
            $table->foreignUuid('countent_uuid');
            $table->string('type_content');//اجار ||بيع
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
