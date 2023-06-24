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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('name');
            $table->string('price');
            $table->string('type');
            $table->foreignUuid('user_uuid');
            $table->string('status')->default(1);
            $table->double('view')->default(0);
            $table->foreignUuid('category_uuid');
            $table->foreignUuid('sup_category_uuid');
            $table->text('details');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
