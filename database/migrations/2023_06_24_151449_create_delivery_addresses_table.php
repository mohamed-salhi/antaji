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
        Schema::create('delivery_addresses', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->double('address');
            $table->double('lat');
            $table->double('lng');
            $table->foreignUuid('user_uuid')->nullable()->references('uuid')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('country_uuid')->nullable()->references('uuid')->on('countries')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('city_uuid')->nullable()->references('uuid')->on('cities')->nullOnDelete()->cascadeOnUpdate();
            $table->boolean('default')->default(false);
            $table->string('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_addresses');
    }
};
