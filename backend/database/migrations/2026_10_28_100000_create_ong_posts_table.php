<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ong_posts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->string('cover_path')->nullable();
            $table->text('body');
            $table->dateTime('published_at')->nullable();
            $table->foreignId('ong_id')->nullable()->constrained('ongs')->cascadeOnDelete();
            $table->foreignId('author_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pet_id')->nullable()->constrained('pets')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('deleted_at');
            $table->index(['ong_id', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ong_posts');
    }
};
