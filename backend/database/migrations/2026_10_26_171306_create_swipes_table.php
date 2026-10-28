<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('swipes', function (Blueprint $table) {
            $table->id();
            $table->enum('action', ['like', 'dislike', 'superlike']);
            $table->foreignId('swiper_pet_id')->constrained('pets')->cascadeOnDelete();
            $table->foreignId('target_pet_id')->constrained('pets')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['swiper_pet_id', 'target_pet_id']);
            $table->index(['target_pet_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('swipes');
    }
};
