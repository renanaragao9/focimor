<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('color', 50)->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->text('bio')->nullable();
            $table->date('birth_date')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_neutered')->default(false);
            $table->boolean('is_vaccinated')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('species_id')->constrained('species');
            $table->foreignId('breed_id')->nullable()->constrained('breeds')->nullOnDelete();
            $table->foreignId('size_id')->nullable()->constrained('sizes')->nullOnDelete();
            $table->foreignId('goal_id')->constrained('goals');
            $table->foreignId('ong_id')->nullable()->constrained('ongs')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('deleted_at');
            $table->index(['species_id', 'is_active']);
            $table->index(['latitude', 'longitude']);
            $table->index('goal_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
