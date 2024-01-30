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
        Schema::create('english_requests', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('skill');
            $table->unsignedBigInteger('user_id');
            $table->string('hash')->unique();
            $table->json('response')->nullable();
            $table->json('extra_data')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('english_requests');
    }
};
