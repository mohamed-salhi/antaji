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
        Schema::create('locations', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('name');
            $table->foreignUuid('user_uuid');
            $table->string('status')->default(1);
            $table->double('view')->default(0);
            $table->double('lat')->nullable();
            $table->double('lng')->nullable();
            $table->string('address')->nullable();
            $table->string('price');
            $table->foreignUuid('category_contents_uuid');
            $table->text('details');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
