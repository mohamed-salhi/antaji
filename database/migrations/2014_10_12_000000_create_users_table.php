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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('name');
            $table->string('status',1)->default(1);
            $table->string('specialization_uuid')->nullable();
            $table->double('address')->nullable();
            $table->double('lat')->nullable();
            $table->double('lng')->nullable();
            $table->text('brief')->nullable();
            $table->string('email')->unique();
            $table->string('mobile')->unique();
            $table->foreignUuid('city_uuid')->nullable()->references('uuid')->on('cities')->nullOnDelete();
            $table->foreignUuid('country_uuid')->nullable()->references('uuid')->on('countries')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('type',['artist','user']);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
