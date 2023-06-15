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
        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid()->index();
            $table->string('name');
            $table->string('email');
            $table->text('description');
            $table->enum('view',[1,2])->default(1);
            $table->enum('importance',[1,2,3])->default(1);
//            $table->foreignUuid('user_uuid')->references('uuid')->on('users')->cascadeOnDelete()->cascadeOnUpdate();;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
