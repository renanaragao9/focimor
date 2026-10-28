<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('breeds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->unsignedSmallInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('species_id')->constrained('species')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['species_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breeds');
    }
};
