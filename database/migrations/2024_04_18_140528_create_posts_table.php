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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('title')->nullable();
            $table->longText('description')->nullable();
            $table->longText('content')->nullable();
            $table->longText('hash_tags')->nullable();
            $table->text('tags')->nullable();
            $table->enum('type', ['photo', 'video', 'story'])->nullable();
            $table->enum('media_type', ['photo', 'video'])->nullable();
            $table->string('thumbnail', 2048)->nullable();
            $table->text('link')->nullable();
            $table->longText('music')->nullable();
            $table->integer('views')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
