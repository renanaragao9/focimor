<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_photos', function (Blueprint $table) {
            $table->id();
            $table->string('path', 500);
            $table->unsignedSmallInteger('order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->foreignId('pet_id')->constrained('pets')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['pet_id', 'is_primary']);
            $table->index(['pet_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_photos');
    }
};
