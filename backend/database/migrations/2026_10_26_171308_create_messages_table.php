<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->text('body');
            $table->dateTime('read_at')->nullable();
            $table->foreignId('pet_match_id')->nullable()->constrained('pet_matches')->cascadeOnDelete();
            $table->foreignId('adoption_match_id')->nullable()->constrained('adoption_matches')->cascadeOnDelete();
            $table->foreignId('sender_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['pet_match_id', 'created_at']);
            $table->index(['adoption_match_id', 'created_at']);
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
