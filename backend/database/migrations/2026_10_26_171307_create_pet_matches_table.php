<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_matches', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->dateTime('matched_at')->useCurrent();
            $table->boolean('is_active')->default(true);
            $table->foreignId('pet_one_id')->constrained('pets')->cascadeOnDelete();
            $table->foreignId('pet_two_id')->constrained('pets')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['pet_one_id', 'pet_two_id']);
            $table->index('pet_two_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_matches');
    }
};
