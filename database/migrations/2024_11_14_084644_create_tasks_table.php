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
            $table->ulid('parent_id')->nullable()->index();
            $table->enum('type', ['single', 'multiple']);
            $table->string('status', 20)->nullable()->index();
            $table->string('cleaning_model');
            $table->string('original_filename')->nullable();
            $table->char('segmentation_id', 25)->nullable()->index();
            $table->json('time')->nullable();
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
