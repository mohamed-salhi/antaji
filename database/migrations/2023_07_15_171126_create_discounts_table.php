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
        Schema::create('discounts', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('name');
            $table->string('code');
            $table->integer('number_of_usage');
            $table->integer('number_of_usage_for_user');
            $table->double('discount');
            $table->string('discount_type');
            $table->integer('date_from');
            $table->double('date_to');
            $table->string('status')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
