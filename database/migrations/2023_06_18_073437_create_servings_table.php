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
        Schema::create('servings', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('name');
            $table->string('status')->default(1);
            $table->double('lat')->nullable();
            $table->double('lng')->nullable();
            $table->string('address')->nullable();
            $table->foreignUuid('category_contents_uuid');
            $table->foreignUuid('user_uuid');
            $table->double('view')->default(0);
            $table->string('working_condition');
            $table->date('from');
            $table->date('to');
            $table->foreignUuid('city_uuid');
            $table->string('price');
            $table->text('details');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servings');
    }
};
