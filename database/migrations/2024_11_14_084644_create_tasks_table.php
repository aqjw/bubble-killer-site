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
        Schema::create('tasks', function (Blueprint $table) {
            $table->ulid('id', 36)->primary();
            $table->ulid('parent_id', 36)->nullable()->index();
            $table->enum('type', ['single', 'multiple']);
            $table->unsignedTinyInteger('status')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('cleaning_model');
            $table->string('original_filename')->nullable();
            $table->json('execution_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
